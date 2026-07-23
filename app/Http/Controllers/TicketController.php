<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Ticket;
use App\Models\User;
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
    | Nota: ya NO pasamos un $tecnicos global. Cada fila de la tabla calcula
    | sus técnicos disponibles vía $ticket->tecnicosDisponibles().
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
            // Ya NO se pasa $tecnicos global — la vista calcula por ticket
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Solo técnicos con cobertura del empresa+sede del ticket
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
    | CREATE — Incluye coberturas para filtrado dinámico en Alpine
    |--------------------------------------------------------------------------
    */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->can('tickets.crear'), 403);

        $esAdmin = $user->hasAnyRole(['admin', 'super-admin']);

        if ($esAdmin) {
            $tecnicos = User::tecnicos()->activos()->orderBy('name')->get(['id', 'name']);

            // Coberturas para filtrar en cliente cuando admin cambia empresa/sede
            $coberturas = DB::table('empresa_user')
                ->whereIn('user_id', $tecnicos->pluck('id'))
                ->get(['user_id', 'empresa_id', 'sede_id']);

            return view('tickets.create', [
                'vista'              => 'admin',
                'empresas'           => Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']),
                'sedesPorEmpresa'    => Sede::activas()->orderBy('nombre')->get(['id', 'nombre', 'empresa_id'])->groupBy('empresa_id'),
                'usuariosPorEmpresa' => User::activos()
                    ->whereNotNull('empresa_id')
                    ->orderBy('name')
                    ->get(['id', 'name', 'empresa_id'])
                    ->groupBy('empresa_id'),
                'tecnicos'           => $tecnicos,
                'coberturas'         => $coberturas,
            ]);
        }

        if (! $user->empresa_id) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una empresa asignada. Contacta al administrador para poder crear tickets.');
        }

        return view('tickets.create', ['vista' => 'usuario']);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Si admin asigna al crear, valida cobertura del tecnico elegido
    |--------------------------------------------------------------------------
    */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $esAdmin = $user->hasAnyRole(['admin', 'super-admin']);

        // Si admin asigna técnico al crear, verificar cobertura antes de la transacción
        if ($esAdmin && $request->filled('asignado_a')) {
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

        $ticket = DB::transaction(function () use ($request, $user, $esAdmin) {

            $data = $request->safe()->only(['titulo', 'categoria', 'prioridad', 'descripcion']);

            if ($esAdmin) {
                $data['solicitante_id'] = $request->solicitante_id;
                $data['empresa_id']     = $request->empresa_id;
                $data['sede_id']        = $request->sede_id;

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

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->id} creado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | ASIGNAR — Valida que el tecnico tenga cobertura para este ticket
    |--------------------------------------------------------------------------
    */
    public function asignar(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.asignar'), 403);
        abort_if(in_array($ticket->estado, ['cerrado', 'cancelado']), 422,
            'No se puede asignar un ticket cerrado o cancelado.');

        $data = $request->validate([
            'tecnico_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        // 1. Tecnico activo y con rol tecnico
        $tecnico = User::where('id', $data['tecnico_id'])
            ->where('activo', true)
            ->role('tecnico')
            ->first();

        if (! $tecnico) {
            return back()->withErrors([
                'tecnico_id' => 'El usuario seleccionado no es un técnico activo válido.',
            ]);
        }

        // 2. NUEVO: validar cobertura empresa+sede via empresa_user
        if (! $this->tecnicoTieneCobertura($tecnico->id, $ticket->empresa_id, $ticket->sede_id)) {
            return back()->withErrors([
                'tecnico_id' => 'Este técnico no tiene cobertura asignada para la empresa/sede de este ticket.',
            ]);
        }

        $primeraAsignacion = is_null($ticket->asignado_a);

        $ticket->update([
            'asignado_a'  => $tecnico->id,
            'estado'      => $ticket->estado === 'en_progreso' ? 'en_progreso' : 'asignado',
            'asignado_at' => $primeraAsignacion ? now() : $ticket->asignado_at,
        ]);

        $msg = $primeraAsignacion
            ? "Ticket #{$ticket->id} asignado a {$tecnico->name}."
            : "Ticket #{$ticket->id} reasignado a {$tecnico->name}.";

        return back()->with('success', $msg);
    }

    /*
    |--------------------------------------------------------------------------
    | INICIAR, RESOLVER, CERRAR, CANCELAR, DESTROY — sin cambios
    |--------------------------------------------------------------------------
    */
    public function iniciar(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('tickets.iniciar'), 403);
        abort_unless($ticket->asignado_a === $user->id, 403,
            'Solo el técnico asignado puede iniciar este ticket.');
        abort_unless($ticket->puedeIniciar(), 422,
            'El ticket no se puede iniciar en su estado actual.');

        $ticket->update(['estado' => 'en_progreso', 'iniciado_at' => now()]);

        return back()->with('success', "Ticket #{$ticket->id} iniciado.");
    }

    public function resolver(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('tickets.resolver'), 403);
        abort_unless($ticket->asignado_a === $user->id, 403,
            'Solo el técnico asignado puede resolver este ticket.');
        abort_unless($ticket->puedeResolver(), 422,
            'El ticket debe estar en progreso para marcarlo como resuelto.');

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

        return back()->with('success', "Ticket #{$ticket->id} marcado como resuelto.");
    }

    public function cerrar(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.cerrar'), 403);
        abort_unless($ticket->puedeCerrar(), 422,
            'El ticket no se puede cerrar en su estado actual.');

        $ticket->update(['estado' => 'cerrado', 'cerrado_at' => now()]);

        return back()->with('success', "Ticket #{$ticket->id} cerrado.");
    }

    public function cancelar(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->can('tickets.cancelar'), 403);

        if (! $user->hasAnyRole(['admin', 'super-admin'])) {
            abort_unless($ticket->solicitante_id === $user->id, 403,
                'Solo puedes cancelar tus propios tickets.');
        }

        abort_unless($ticket->puedeCancelar(), 422,
            'El ticket no se puede cancelar en su estado actual.');

        $ticket->update(['estado' => 'cancelado']);

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
     * Verifica si un técnico tiene cobertura asignada en empresa_user
     * para el contexto (empresa, sede) dado.
     *
     * Si $empresaId es null, devuelve true (no hay restricción).
     */
    private function tecnicoTieneCobertura(int $tecnicoId, ?int $empresaId, ?int $sedeId): bool
    {
        if (! $empresaId) return true;

        return DB::table('empresa_user')
            ->where('user_id', $tecnicoId)
            ->where('empresa_id', $empresaId)
            ->where(function ($q) use ($sedeId) {
                $q->whereNull('sede_id');
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
            ->when($request->filled('estado'),     fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('prioridad'),  fn($q) => $q->where('prioridad', $request->prioridad))
            ->when($request->filled('categoria'),  fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->filled('empresa_id'), fn($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->boolean('sin_asignar'), fn($q) => $q->whereNull('asignado_a'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn($sub) => $sub
                    ->where('titulo', 'like', $term)
                    ->orWhere('descripcion', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['estado', 'prioridad', 'categoria', 'empresa_id', 'sin_asignar', 'q']))
            ->filter(fn($v) => $v !== null && $v !== '')
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
