<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipoRequest;
use App\Http\Requests\UpdateEquipoRequest;
use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $empresas = $this->empresasDisponiblesPara($user);
        $sedes    = $this->sedesDisponiblesPara($user, $empresas->pluck('id'));

        $query = $this->scopePorRol($user);
        $this->aplicarFiltros($query, $request);

        $equipos = $query
            ->with(['empresa:id,nombre', 'sede:id,nombre', 'responsable:id,name'])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('equipos.index', [
            'equipos'         => $equipos,
            'filtros'         => $this->filtrosActivos($request),
            'empresas'        => $empresas,
            'sedesPorEmpresa' => $sedes->groupBy('empresa_id'),
            'resumen'         => $this->resumenPorUbicacion($user, $request, $empresas, $sedes),
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

        try {
            $equipo = Equipo::create($data);
        } catch (UniqueConstraintViolationException) {
            // El request ya valida el serial; esto solo cubre dos registros simultáneos
            return $this->serialDuplicado();
        }

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

        try {
            $equipo->update($this->extraerDatosValidados($request));
        } catch (UniqueConstraintViolationException) {
            return $this->serialDuplicado();
        }

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
     * Respaldo ante el índice único de la BD (condición de carrera entre dos guardados).
     */
    private function serialDuplicado(): RedirectResponse
    {
        return back()
            ->withInput()
            ->withErrors(['numero_serie' => 'El número de serie ya está registrado en otro equipo del sistema.']);
    }

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

        $sedesPorEmpresa = $this->sedesDisponiblesPara($user, $empresaIds)->groupBy('empresa_id');

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

    /**
     * Sedes activas que el usuario puede operar, de las empresas indicadas.
     */
    private function sedesDisponiblesPara(User $user, Collection $empresaIds): Collection
    {
        $query = $user->hasAnyRole(['admin', 'super-admin'])
            ? Sede::activas()->whereIn('empresa_id', $empresaIds)
            : $user->sedesAccesibles()->where('activo', true);

        return $query->orderBy('nombre')->get(['id', 'nombre', 'empresa_id']);
    }

    /**
     * Árbol empresa → sedes con la cantidad de equipos de cada sede.
     *
     * Respeta el alcance del rol y los filtros de búsqueda, categoría y estado,
     * pero no los de empresa/sede: es el menú desde el que se eligen.
     * Incluye las sedes sin equipos y, si los hay, los equipos sin sede.
     */
    private function resumenPorUbicacion(User $user, Request $request, Collection $empresas, Collection $sedes): Collection
    {
        $alcance = $this->scopePorRol($user);
        $this->aplicarFiltros($alcance, $request, conUbicacion: false);

        // Subconsulta por id: el agrupado funciona aunque el scope del rol use joins o selects propios
        $conteos = Equipo::query()
            ->whereIn('id', $alcance->select('equipos.id'))
            ->selectRaw('empresa_id, sede_id, COUNT(*) AS total')
            ->groupBy('empresa_id', 'sede_id')
            ->toBase()
            ->get();

        // Equipos de empresas o sedes que ya no están activas: se agregan para no perderlos del conteo
        $empresas = $empresas->concat(
            Empresa::whereIn('id', $conteos->pluck('empresa_id')->diff($empresas->pluck('id')))->get(['id', 'nombre'])
        );
        $sedes = $sedes->concat(
            Sede::whereIn('id', $conteos->pluck('sede_id')->filter()->diff($sedes->pluck('id')))->get(['id', 'nombre', 'empresa_id'])
        );

        $totalPorSede = $conteos->groupBy('empresa_id')
            ->map(fn ($filas) => $filas->pluck('total', 'sede_id')->map(fn ($t) => (int) $t));

        return $empresas
            ->map(function ($empresa) use ($sedes, $totalPorSede) {
                $totales = $totalPorSede->get($empresa->id, collect());

                return [
                    'id'       => $empresa->id,
                    'nombre'   => $empresa->nombre,
                    'total'    => $totales->sum(),
                    'sin_sede' => $totales->get('', 0),
                    'sedes'    => $sedes->where('empresa_id', $empresa->id)
                        ->sortBy('nombre')
                        ->map(fn ($sede) => [
                            'id'     => $sede->id,
                            'nombre' => $sede->nombre,
                            'total'  => $totales->get($sede->id, 0),
                        ])
                        ->values(),
                ];
            })
            ->sortBy('nombre')
            ->values();
    }

    private function scopePorRol(User $user): Builder
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return Equipo::query();
        }
        return Equipo::accesiblesParaTecnico($user);
    }

    /**
     * @param bool $conUbicacion false para ignorar empresa y sede (lo usa el resumen lateral)
     */
    private function aplicarFiltros(Builder $query, Request $request, bool $conUbicacion = true): void
    {
        $query
            ->when($request->filled('categoria'),  fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->filled('estado'),     fn($q) => $q->where('estado', $request->estado))
            ->when($conUbicacion && $request->filled('empresa_id'), fn($q) => $q->where('empresa_id', $request->empresa_id))
            // sede_id=sin → equipos de la empresa que no tienen sede asignada
            ->when($conUbicacion && $request->filled('sede_id'), fn($q) => $request->sede_id === 'sin'
                ? $q->whereNull('sede_id')
                : $q->where('sede_id', $request->sede_id))
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
        return collect($request->only(['categoria', 'estado', 'empresa_id', 'sede_id', 'q']))
            ->filter(fn($v) => $v !== null && $v !== '')
            ->toArray();
    }

    
}
