<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proyectos\ProyectoRequest;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\ProyectoEtapa;
use App\Models\ProyectoSolicitud;
use App\Models\User;
use App\Services\Proyectos\ProyectoService;
use App\Services\Proyectos\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProyectoController extends Controller
{
    public function __construct(
        private readonly ProyectoService $proyectos,
        private readonly TimelineService $timeline,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX — Bandeja de proyectos
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Proyecto::class);

        $user    = $request->user();
        $filtros = collect($request->only(['q', 'estado', 'cliente_id', 'mios']))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->all();

        $proyectos = Proyecto::query()
            ->visiblesPara($user)
            ->buscar($filtros['q'] ?? null)
            ->when($filtros['estado'] ?? null, fn ($q, $v) => $q->where('estado', $v))
            ->when($filtros['cliente_id'] ?? null, fn ($q, $v) => $q->where('cliente_id', $v))
            ->when($filtros['mios'] ?? null, fn ($q) => $q->whereHas('equipo', fn ($e) => $e->where('users.id', $user->id)))
            ->with(['cliente:id,nombre', 'responsable:id,name'])
            ->withCount('solicitudesAbiertas')
            // Última etapa real (los registros planeados a futuro no cuentan como actividad)
            ->withMax(['etapas' => fn ($q) => $q->where('estado', '!=', ProyectoEtapa::ESTADO_PLANEADA)], 'fecha')
            ->orderByRaw("CASE estado WHEN 'activo' THEN 0 WHEN 'pausado' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('proyectos.index', [
            'proyectos'   => $proyectos,
            'clientes'    => $clientes,
            'filtros'     => $filtros,
            'puedeCrear'  => $user->can('create', Proyecto::class),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / STORE
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View
    {
        Gate::authorize('create', Proyecto::class);

        $proyecto = new Proyecto([
            'estado'         => Proyecto::ESTADO_ACTIVO,
            'fecha_inicio'   => now(),
            'responsable_id' => $request->user()->id,
            'cliente_id'     => $request->integer('cliente_id') ?: null,
        ]);

        return view('proyectos.create', $this->datosFormulario($proyecto));
    }

    public function store(ProyectoRequest $request): RedirectResponse
    {
        $proyecto = $this->proyectos->crear($request->validated(), $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', "Proyecto {$proyecto->codigo} creado.");
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detalle con línea de tiempo
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, Proyecto $proyecto): View
    {
        Gate::authorize('view', $proyecto);

        $proyecto->load([
            'cliente',
            'responsable:id,name',
            'equipo:users.id,users.name',
            'etapas' => fn ($q) => $q->with(['autor:id,name', 'adjuntos', 'solicitudesAtendidas:id,titulo,etapa_resolucion_id']),
            'solicitudes' => fn ($q) => $q->with(['autor:id,name', 'adjuntos', 'etapaResolucion:id,titulo,tipo']),
            'actividades.usuario:id,name',
        ]);

        $user = $request->user();

        $puede = [
            'editar'    => $user->can('update', $proyecto),
            'eliminar'  => $user->can('delete', $proyecto),
            'registrar' => $user->can('registrar', $proyecto),
        ];

        // Qué registros puede modificar este usuario (autor, responsable o editor)
        $modificables = [
            'etapas'      => $proyecto->etapas->filter(fn ($e) => $user->can('modificarRegistro', [$proyecto, $e]))->pluck('id')->all(),
            'solicitudes' => $proyecto->solicitudes->filter(fn ($s) => $user->can('modificarRegistro', [$proyecto, $s]))->pluck('id')->all(),
        ];

        return view('proyectos.show', [
            'proyecto'            => $proyecto,
            'timeline'            => $this->timeline->construir($proyecto),
            'economia'            => $proyecto->resumenEconomico(),
            'resumenEtapas'       => $this->resumenPorTipo($proyecto->etapas),
            'solicitudesAbiertas' => $proyecto->solicitudes
                ->filter->estaAbierta()
                ->sortByDesc(fn ($s) => $s->fecha_solicitud->format('Y-m-d'))
                ->values(),
            'kpis'                => $this->kpis($proyecto),
            'puede'               => $puede,
            'modificables'        => $modificables,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT / UPDATE / DESTROY
    |--------------------------------------------------------------------------
    */
    public function edit(Proyecto $proyecto): View
    {
        Gate::authorize('update', $proyecto);

        $proyecto->load('equipo:users.id,users.name');

        return view('proyectos.edit', $this->datosFormulario($proyecto));
    }

    public function update(ProyectoRequest $request, Proyecto $proyecto): RedirectResponse
    {
        $this->proyectos->actualizar($proyecto, $request->validated(), $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', 'Proyecto actualizado.');
    }

    public function destroy(Proyecto $proyecto): RedirectResponse
    {
        Gate::authorize('delete', $proyecto);

        $proyecto->delete();

        return redirect()
            ->route('proyectos.index')
            ->with('exito', "Proyecto {$proyecto->codigo} eliminado.");
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers privados
    |--------------------------------------------------------------------------
    */

    private function datosFormulario(Proyecto $proyecto): array
    {
        $idsEquipo = $proyecto->exists ? $proyecto->equipo->pluck('id')->all() : [];

        // Usuarios elegibles: los que tienen acceso al módulo + los que ya están en el equipo
        $usuarios = User::query()
            ->where(fn ($q) => $q
                ->permission(['proyectos.ver', 'proyectos.ver-todos'])
                ->orWhereIn('id', $idsEquipo ?: [0])
            )
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $clientes = Cliente::query()
            ->where(fn ($q) => $q->where('activo', true)->orWhere('id', $proyecto->cliente_id))
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'numero_documento']);

        $rolesEquipo = $proyecto->exists
            ? $proyecto->equipo->mapWithKeys(fn ($u) => [$u->id => $u->pivot->rol])->all()
            : [];

        return compact('proyecto', 'usuarios', 'clientes', 'rolesEquipo');
    }

    /** Conteo y última fecha por tipo de etapa (para el mapa lateral y la numeración automática) */
    private function resumenPorTipo(Collection $etapas): array
    {
        $resumen = [];

        foreach (array_keys(ProyectoEtapa::TIPOS) as $tipo) {
            $delTipo = $etapas->where('tipo', $tipo);

            $resumen[$tipo] = [
                'cantidad' => $delTipo->count(),
                'ultima'   => $delTipo->max(fn ($e) => $e->fecha->format('Y-m-d')),
            ];
        }

        return $resumen;
    }

    private function kpis(Proyecto $proyecto): array
    {
        $hoy        = now()->startOfDay();
        $inicio     = $proyecto->fecha_inicio->copy()->startOfDay();
        $fin        = $proyecto->fecha_cierre ?? $hoy;
        $transcurridos = max(0, (int) $inicio->diffInDays($fin));

        $totalDias = $proyecto->fecha_entrega_estimada
            ? max(1, (int) $inicio->diffInDays($proyecto->fecha_entrega_estimada))
            : null;

        return [
            'dias_transcurridos' => $transcurridos,
            'dias_totales'       => $totalDias,
            'porcentaje_tiempo'  => $totalDias ? (int) min(100, round($transcurridos / $totalDias * 100)) : null,
            'vencido'            => $proyecto->fecha_entrega_estimada
                && ! $proyecto->estaCerrado()
                && $proyecto->fecha_entrega_estimada->lt($hoy),
            'entregas'           => $proyecto->etapas->where('tipo', ProyectoEtapa::TIPO_ENTREGA)->count(),
            'solicitudes_abiertas' => $proyecto->solicitudes->whereIn('estado', ProyectoSolicitud::ESTADOS_ABIERTOS)->count(),
        ];
    }
}
