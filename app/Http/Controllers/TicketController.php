<?php

namespace App\Http\Controllers;

use App\Events\Tickets\TicketAsignado;
use App\Events\Tickets\TicketCancelado;
use App\Events\Tickets\TicketCerrado;
use App\Events\Tickets\TicketCreado;
use App\Events\Tickets\TicketIniciado;
use App\Events\Tickets\TicketResuelto;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Ticket;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    | Cada fila de la tabla calcula sus técnicos disponibles vía
    | $ticket->tecnicosDisponibles().
    */
    public function index(Request $request): View
    {
        $user = $request->user();

        $modo  = $this->modoListadoParaUsuario($user);
        $query = $this->scopePorPermiso($user, $modo);
        $this->aplicarFiltros($query, $request);

        $tickets = $query
            ->with(['solicitante:id,name', 'asignado:id,name', 'empresa:id,nombre', 'sede:id,nombre'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', [
            'tickets'  => $tickets,
            'modo'     => $modo,
            'filtros'  => $this->filtrosActivos($request),
            'empresas' => $user->can('tickets.ver-todos')
                ? Empresa::activas()->orderBy('nombre')->get(['id', 'nombre'])
                : collect(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Solo técnicos con cobertura de la empresa+sede del ticket
    |--------------------------------------------------------------------------
    */
    public function show(Ticket $ticket): View
    {
        $this->autorizarVista($ticket);

        $ticket->load([
            'solicitante:id,name,email',
            'asignado:id,name,email',
            'empresa:id,nombre',
            'sede:id,nombre,ciudad,direccion',
        ]);

        $user = Auth::user();

        return view('tickets.show', [
            'ticket'   => $ticket,
            'tecnicos' => $user->can('tickets.asignar')
                ? $ticket->tecnicosDisponibles()
                : collect(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    | Dos vistas posibles según el rol:
    |  - 'admin'   → el usuario ELIGE empresa/sede destino. Aplica a roles
    |                transversales (ven todas) y con cobertura (ven las suyas).
    |  - 'usuario' → el ticket HEREDA la empresa/sede del solicitante.
    */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->can('tickets.crear'), 403);

        // ── Caso A: elige el destino del ticket ──
        if ($this->puedeElegirDestino($user)) {

            $empresasDisponibles = $this->empresasDisponiblesPara($user);

            if ($empresasDisponibles->isEmpty()) {
                return redirect()->route('dashboard')->with(
                    'error',
                    'No tienes empresas asignadas en tu cobertura. Contacta al administrador.'
                );
            }

            $idsEmpresas = $empresasDisponibles->pluck('id');

            $tecnicos = User::activos()
                ->role(Roles::CON_COBERTURA)
                ->orderBy('name')
                ->get(['id', 'name']);

            // Coberturas para filtrar técnicos en cliente al cambiar empresa/sede
            $coberturas = DB::table('empresa_user')
                ->whereIn('user_id', $tecnicos->pluck('id'))
                ->get(['user_id', 'empresa_id', 'sede_id']);

            return view('tickets.create', [
                'vista'              => 'admin',
                'empresas'           => $empresasDisponibles,
                'sedesPorEmpresa'    => Sede::activas()
                    ->whereIn('empresa_id', $idsEmpresas)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'empresa_id'])
                    ->groupBy('empresa_id'),
                'usuariosPorEmpresa' => User::activos()
                    ->whereIn('empresa_id', $idsEmpresas)
                    ->orderBy('name')
                    ->get(['id', 'name', 'empresa_id'])
                    ->groupBy('empresa_id'),
                'tecnicos'           => $tecnicos,
                'coberturas'         => $coberturas,
            ]);
        }

        // ── Caso B: hereda su propia empresa ──
        if (! $user->empresa_id) {
            return redirect()->route('dashboard')->with(
                'error',
                'No tienes una empresa asignada. Contacta al administrador para poder crear tickets.'
            );
        }

        return view('tickets.create', ['vista' => 'usuario']);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $user         = Auth::user();
        $eligeDestino = $this->puedeElegirDestino($user);

        // 1. La empresa elegida debe estar dentro del alcance del usuario
        if ($eligeDestino && $request->filled('empresa_id')) {
            $permitidas = $this->empresasDisponiblesPara($user)->pluck('id');

            if (! $permitidas->contains((int) $request->empresa_id)) {
                return back()
                    ->withInput()
                    ->withErrors(['empresa_id' => 'No tienes cobertura sobre la empresa seleccionada.']);
            }
        }

        // 2. Si asigna técnico al crear, verificar su cobertura
        if ($eligeDestino && $request->filled('asignado_a')) {
            if (! $this->tecnicoTieneCobertura(
                (int) $request->asignado_a,
                (int) $request->empresa_id,
                $request->sede_id ? (int) $request->sede_id : null
            )) {
                return back()
                    ->withInput()
                    ->withErrors(['asignado_a' => 'El técnico elegido no tiene cobertura para esta empresa/sede.']);
            }
        }

        $ticket = DB::transaction(function () use ($request, $user, $eligeDestino) {

            $data = $request->safe()->only(['titulo', 'categoria', 'prioridad', 'descripcion']);

            if ($eligeDestino) {
                // El solicitante puede ser otro usuario, o el propio creador
                $data['solicitante_id'] = $request->filled('solicitante_id')
                    ? $request->solicitante_id
                    : $user->id;
                $data['empresa_id'] = $request->empresa_id;
                $data['sede_id']    = $request->sede_id;

                if ($request->filled('asignado_a')) {
                    $data['asignado_a']  = $request->asignado_a;
                    $data['estado']      = 'asignado';
                    $data['asignado_at'] = now();
                } else {
                    $data['estado'] = 'abierto';
                }
            } else {
                $data['solicitante_id'] = $user->id;
                $data['empresa_id']     = $user->empresa_id;
                $data['sede_id']        = $user->sede_id;
                $data['estado']         = 'abierto';
            }

            if ($request->hasFile('foto_solicitud')) {
                $data['foto_solicitud'] = $request->file('foto_solicitud')
                    ->store('tickets/solicitudes', 'local');
            }

            return Ticket::create($data);
        });

        // Disparar evento según si quedó asignado o no
        if ($ticket->asignado_a) {
            event(new TicketAsignado(
                $ticket->fresh(['solicitante', 'asignado', 'empresa', 'sede']),
                $user
            ));
        } else {
            event(new TicketCreado($ticket->fresh(['solicitante', 'empresa'])));
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->id} creado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | ASIGNAR — Valida que el técnico tenga cobertura para este ticket
    |--------------------------------------------------------------------------
    */
    public function asignar(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.asignar'), 403);
        abort_if(
            in_array($ticket->estado, ['cerrado', 'cancelado']),
            422,
            'No se puede asignar un ticket cerrado o cancelado.'
        );

        $data = $request->validate([
            'tecnico_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        // 1. Usuario activo con un rol que puede recibir asignaciones
        $tecnico = User::where('id', $data['tecnico_id'])
            ->where('activo', true)
            ->role(Roles::CON_COBERTURA)
            ->first();

        if (! $tecnico) {
            return back()->withErrors([
                'tecnico_id' => 'El usuario seleccionado no puede recibir asignaciones de tickets.',
            ]);
        }

        // 2. Validar cobertura empresa+sede vía empresa_user
        if (! $this->tecnicoTieneCobertura($tecnico->id, $ticket->empresa_id, $ticket->sede_id)) {
            return back()->withErrors([
                'tecnico_id' => 'Este técnico no tiene cobertura asignada para la empresa/sede de este ticket.',
            ]);
        }

        // Capturar quién era el técnico anterior ANTES del update
        $asignadoAnteriorId = $ticket->asignado_a;
        $primeraAsignacion  = is_null($asignadoAnteriorId);

        $ticket->update([
            'asignado_a'  => $tecnico->id,
            'estado'      => $ticket->estado === 'en_progreso' ? 'en_progreso' : 'asignado',
            'asignado_at' => $primeraAsignacion ? now() : $ticket->asignado_at,
        ]);

        // Resolver técnico anterior solo si es distinto del nuevo
        $tecnicoAnterior = null;
        if ($asignadoAnteriorId && $asignadoAnteriorId !== $tecnico->id) {
            $tecnicoAnterior = User::find($asignadoAnteriorId);
        }

        event(new TicketAsignado(
            $ticket->fresh(['solicitante', 'asignado', 'empresa', 'sede']),
            $request->user(),
            $tecnicoAnterior,
        ));

        $msg = $primeraAsignacion
            ? "Ticket #{$ticket->id} asignado a {$tecnico->name}."
            : "Ticket #{$ticket->id} reasignado a {$tecnico->name}.";

        return back()->with('success', $msg);
    }

    /*
    |--------------------------------------------------------------------------
    | CICLO DE VIDA
    |--------------------------------------------------------------------------
    */
    public function iniciar(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('tickets.iniciar'), 403);
        abort_unless(
            $ticket->asignado_a === $user->id,
            403,
            'Solo el técnico asignado puede iniciar este ticket.'
        );
        abort_unless(
            $ticket->puedeIniciar(),
            422,
            'El ticket no se puede iniciar en su estado actual.'
        );

        $ticket->update(['estado' => 'en_progreso', 'iniciado_at' => now()]);

        event(new TicketIniciado($ticket->fresh(['solicitante', 'asignado'])));

        return back()->with('success', "Ticket #{$ticket->id} iniciado.");
    }

    public function resolver(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('tickets.resolver'), 403);
        abort_unless(
            $ticket->asignado_a === $user->id,
            403,
            'Solo el técnico asignado puede resolver este ticket.'
        );
        abort_unless(
            $ticket->puedeResolver(),
            422,
            'El ticket debe estar en progreso para marcarlo como resuelto.'
        );

        $data = $request->validate([
            'notas_resolucion'     => ['required', 'string', 'min:10', 'max:3000'],
            'evidencia_resolucion' => ['nullable', 'image', 'max:5120'],
        ], [
            'notas_resolucion.required' => 'Debes describir cómo resolviste el problema.',
            'notas_resolucion.min'      => 'Las notas deben tener al menos :min caracteres.',
        ]);

        $update = [
            'notas_resolucion' => $data['notas_resolucion'],
            'estado'           => 'resuelto',
            'resuelto_at'      => now(),
        ];

        if ($request->hasFile('evidencia_resolucion')) {
            $update['evidencia_resolucion'] = $request->file('evidencia_resolucion')
                ->store('tickets/evidencias', 'local');
        }

        $ticket->update($update);

        event(new TicketResuelto($ticket->fresh(['solicitante', 'asignado'])));

        return back()->with('success', "Ticket #{$ticket->id} marcado como resuelto.");
    }

    public function cerrar(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.cerrar'), 403);
        abort_unless(
            $ticket->puedeCerrar(),
            422,
            'El ticket no se puede cerrar en su estado actual.'
        );

        $ticket->update(['estado' => 'cerrado', 'cerrado_at' => now()]);

        event(new TicketCerrado(
            $ticket->fresh(['solicitante', 'asignado']),
            $request->user(),
        ));

        return back()->with('success', "Ticket #{$ticket->id} cerrado.");
    }

    public function cancelar(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->can('tickets.cancelar'), 403);

        // Quien no tenga alcance transversal solo cancela sus propios tickets
        if (! Roles::usuarioCumple($user, 'esTransversal')) {
            abort_unless(
                $ticket->solicitante_id === $user->id,
                403,
                'Solo puedes cancelar tus propios tickets.'
            );
        }

        abort_unless(
            $ticket->puedeCancelar(),
            422,
            'El ticket no se puede cancelar en su estado actual.'
        );

        $ticket->update(['estado' => 'cancelado']);

        event(new TicketCancelado(
            $ticket->fresh(['solicitante', 'asignado']),
            $user,
        ));

        return back()->with('success', "Ticket #{$ticket->id} cancelado.");
    }

    public function destroy(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.eliminar'), 403);

        $id = $ticket->id;

        DB::transaction(function () use ($ticket) {
            if ($ticket->foto_solicitud) {
                Storage::disk('local')->delete($ticket->foto_solicitud);
            }
            if ($ticket->evidencia_resolucion) {
                Storage::disk('local')->delete($ticket->evidencia_resolucion);
            }
            $ticket->delete();
        });

        return redirect()->route('tickets.index')
            ->with('success', "Ticket #{$id} eliminado permanentemente.");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers privados
    // ─────────────────────────────────────────────────────────────────────

    /**
     * ¿Este usuario ELIGE el destino del ticket (empresa/sede), o lo hereda?
     * Se decide por grupo de rol, no por strings sueltos.
     */
    private function puedeElegirDestino(User $user): bool
    {
        return Roles::usuarioCumple($user, 'eligeDestinoTicket');
    }

    /**
     * Empresas sobre las que este usuario puede crear tickets.
     *  - Transversales (admin, gerente, líder de ventas): todas las activas.
     *  - Con cobertura (técnico, líder técnico): solo las de empresa_user.
     *  - Resto: colección vacía (usan la vista 'usuario' y heredan la suya).
     */
    private function empresasDisponiblesPara(User $user)
    {
        if (Roles::usuarioCumple($user, 'esTransversal')) {
            return Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']);
        }

        if (Roles::usuarioCumple($user, 'requiereCobertura')) {
            $ids = DB::table('empresa_user')
                ->where('user_id', $user->id)
                ->distinct()
                ->pluck('empresa_id');

            return Empresa::activas()
                ->whereIn('id', $ids)
                ->orderBy('nombre')
                ->get(['id', 'nombre']);
        }

        return collect();
    }

    /**
     * Verifica si un técnico tiene cobertura asignada en empresa_user
     * para el contexto (empresa, sede) dado.
     *
     * Si $empresaId es null, devuelve true (no hay restricción que validar).
     */
    private function tecnicoTieneCobertura(int $tecnicoId, ?int $empresaId, ?int $sedeId): bool
    {
        if (! $empresaId) {
            return true;
        }

        return DB::table('empresa_user')
            ->where('user_id', $tecnicoId)
            ->where('empresa_id', $empresaId)
            ->where(function ($q) use ($sedeId) {
                // Cobertura global de la empresa (sede_id NULL) siempre sirve
                $q->whereNull('sede_id');
                // O cobertura puntual de esa sede
                if ($sedeId) {
                    $q->orWhere('sede_id', $sedeId);
                }
            })
            ->exists();
    }

    private function modoListadoParaUsuario(User $user): string
    {
        return match (true) {
            $user->can('tickets.ver-todos')     => 'todos',
            $user->can('tickets.ver-asignados') => 'asignados',
            $user->can('tickets.ver-propios')   => 'propios',
            default                             => abort(403, 'No tienes permiso para ver tickets.'),
        };
    }

    private function scopePorPermiso(User $user, string $modo): Builder
    {
        return match ($modo) {
            'todos'     => Ticket::query(),
            'asignados' => Ticket::asignadosA($user->id),
            'propios'   => Ticket::delSolicitante($user->id),
        };
    }

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('estado'),      fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('prioridad'),   fn ($q) => $q->where('prioridad', $request->prioridad))
            ->when($request->filled('categoria'),   fn ($q) => $q->where('categoria', $request->categoria))
            ->when($request->filled('empresa_id'),  fn ($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->boolean('sin_asignar'), fn ($q) => $q->whereNull('asignado_a'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn ($sub) => $sub
                    ->where('titulo', 'like', $term)
                    ->orWhere('descripcion', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['estado', 'prioridad', 'categoria', 'empresa_id', 'sin_asignar', 'q']))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->toArray();
    }

    private function autorizarVista(Ticket $ticket): void
    {
        $user = Auth::user();

        $puede = match (true) {
            $user->can('tickets.ver-todos')     => true,
            $user->can('tickets.ver-asignados') => $ticket->asignado_a === $user->id,
            $user->can('tickets.ver-propios')   => $ticket->solicitante_id === $user->id,
            default                             => false,
        };

        abort_unless($puede, 403, 'No tienes acceso a este ticket.');
    }
}