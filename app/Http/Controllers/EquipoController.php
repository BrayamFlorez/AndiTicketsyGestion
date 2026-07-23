<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipoRequest;
use App\Http\Requests\UpdateEquipoRequest;
use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->can('equipos.ver'), 403);

        $query = $this->scopePorRol($user);
        $this->aplicarFiltros($query, $request);

        $equipos = $query
            ->with(['empresa:id,nombre', 'sede:id,nombre', 'responsable:id,name'])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('equipos.index', [
            'equipos'  => $equipos,
            'filtros'  => $this->filtrosActivos($request),
            'empresas' => $this->empresasDisponiblesPara($user),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->can('equipos.crear'), 403);

        $empresas = $this->empresasDisponiblesPara($user);

        if ($empresas->isEmpty()) {
            return redirect()->route('equipos.index')
                ->with('error', 'No tienes empresas asignadas. Pide al administrador que te asigne al menos una.');
        }

        return view('equipos.create', $this->datosParaFormulario($user, $empresas));
    }

    public function show(Request $request, Equipo $equipo): View
    {
        $user = $request->user();
        abort_unless($user->can('equipos.ver'), 403);
        
        // Validamos que el técnico tenga cobertura sobre este equipo, igual que al editar.
        // (Los administradores pasarán automáticamente gracias a tu método en el modelo)
        abort_unless($equipo->puedeSerGestionadoPor($user), 403,
            'No tienes cobertura para ver el detalle de este equipo.');

        // Cargamos todas las relaciones necesarias para la vista, 
        // incluyendo el historial de mantenimientos
        $equipo->load([
            'empresa', 
            'sede', 
            'responsable', 
            'creador',
            'mantenimientosPreventivos.tecnico' // Cargamos mantenimientos y el técnico de cada uno
        ]);

        return view('equipos.show', compact('equipo'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreEquipoRequest $request): RedirectResponse
    {
        $data = $this->extraerDatosValidados($request);
        $data['creado_por'] = Auth::id();

        $equipo = Equipo::create($data);

        return redirect()
            ->route('equipos.index')
            ->with('success', "Equipo \"{$equipo->nombre}\" registrado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Request $request, Equipo $equipo): View
    {
        $user = $request->user();
        abort_unless($user->can('equipos.editar'), 403);
        abort_unless($equipo->puedeSerGestionadoPor($user), 403,
            'No tienes cobertura para gestionar este equipo.');

        $empresas = $this->empresasDisponiblesPara($user);

        return view('equipos.edit', array_merge(
            $this->datosParaFormulario($user, $empresas),
            ['equipo' => $equipo]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateEquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        abort_unless($equipo->puedeSerGestionadoPor($request->user()), 403,
            'No tienes cobertura para gestionar este equipo.');

        $data = $this->extraerDatosValidados($request);
        $equipo->update($data);

        return redirect()
            ->route('equipos.index')
            ->with('success', "Equipo \"{$equipo->nombre}\" actualizado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — Solo super-admin, solo si NO tiene historial
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Equipo $equipo): RedirectResponse
    {
        // El permiso 'equipos.eliminar' solo lo tiene super-admin (ver seeder)
        abort_unless($request->user()->can('equipos.eliminar'), 403);

        if ($equipo->tieneHistorial()) {
            $razones = implode(', ', $equipo->razonesParaNoEliminar());
            return back()->with('error',
                "No se puede eliminar el equipo \"{$equipo->nombre}\": {$razones}. " .
                "Inhabilítalo en su lugar (dar de baja)."
            );
        }

        $nombre = $equipo->nombre;
        $equipo->delete();

        return redirect()
            ->route('equipos.index')
            ->with('success', "Equipo \"{$nombre}\" eliminado permanentemente.");
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO — Dar de baja, reactivar, marcar en mantenimiento
    |--------------------------------------------------------------------------
    */
    public function cambiarEstado(Request $request, Equipo $equipo): RedirectResponse
    {
        abort_unless($request->user()->can('equipos.editar'), 403);
        abort_unless($equipo->puedeSerGestionadoPor($request->user()), 403,
            'No tienes cobertura para gestionar este equipo.');

        $data = $request->validate([
            'estado' => ['required', Rule::in(array_keys(Equipo::ESTADOS))],
        ]);

        $estadoAnterior = $equipo->label_estado;
        $equipo->update(['estado' => $data['estado']]);

        return back()->with('success',
            "Equipo \"{$equipo->nombre}\": {$estadoAnterior} → {$equipo->label_estado}."
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Extrae datos validados + filtra especificaciones al schema de la categoría.
     * Reutilizado por store() y update() para no duplicar lógica.
     */
    private function extraerDatosValidados(StoreEquipoRequest|UpdateEquipoRequest $request): array
    {
        $data = $request->safe()->only([
            'empresa_id', 'sede_id', 'responsable_id',
            'categoria', 'nombre', 'marca', 'modelo',
            'numero_serie', 'ubicacion', 'estado', 'notas',
        ]);

        $data['estado'] = $data['estado'] ?? 'activo';

        $schema = Equipo::ESPECIFICACIONES_SCHEMA[$data['categoria']] ?? [];
        $data['especificaciones'] = collect($request->input('especificaciones', []))
            ->only(array_keys($schema))
            ->toArray();

        return $data;
    }

    /**
     * Empresas que el usuario puede operar.
     */
    private function empresasDisponiblesPara(User $user)
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']);
        }

        return $user->empresasAsignadas()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['empresas.id', 'empresas.nombre']);
    }

    /**
     * Datos comunes para create/edit (sedes, usuarios filtrados).
     */
    private function datosParaFormulario(User $user, $empresas): array
    {
        $empresaIds = $empresas->pluck('id');

        // Sedes según el rol del usuario
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $sedesPorEmpresa = Sede::activas()
                ->whereIn('empresa_id', $empresaIds)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'empresa_id'])
                ->groupBy('empresa_id');
        } else {
            $sedesPorEmpresa = $user->sedesAccesibles()
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'empresa_id'])
                ->groupBy('empresa_id');
        }

        // Usuarios (responsables) — por empresa
        $usuariosPorEmpresa = User::activos()
            ->whereIn('empresa_id', $empresaIds)
            ->orderBy('name')
            ->get(['id', 'name', 'empresa_id'])
            ->groupBy('empresa_id');

        return [
            'empresas'           => $empresas,
            'sedesPorEmpresa'    => $sedesPorEmpresa,
            'usuariosPorEmpresa' => $usuariosPorEmpresa,
        ];
    }

    private function scopePorRol(User $user): Builder
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return Equipo::query();
        }
        return Equipo::accesiblesParaTecnico($user);
    }

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('categoria'),  fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->filled('estado'),     fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('empresa_id'), fn($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn($sub) => $sub
                    ->where('nombre', 'like', $term)
                    ->orWhere('marca', 'like', $term)
                    ->orWhere('modelo', 'like', $term)
                    ->orWhere('numero_serie', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['categoria', 'estado', 'empresa_id', 'q']))
            ->filter(fn($v) => $v !== null && $v !== '')
            ->toArray();
    }

    
}
