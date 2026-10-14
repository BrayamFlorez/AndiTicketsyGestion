<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraRequest;
use App\Http\Requests\UpdateImpresoraRequest;
use App\Models\Empresa;
use App\Models\Impresora;
use App\Models\ImpresoraModelo;
use App\Models\Sede;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Models\ImpresoraConsumible;

class ImpresoraController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Impresora::query()
            ->with([
                'modelo:id,marca,modelo,tipo_tecnologia,color',
                'empresa:id,nombre',
                'sede:id,nombre',
                'consumiblesActivos',   // ← nuevo
            ]);

        $this->scopePorAlcance($query, $user);
        $this->aplicarFiltros($query, $request);

        $impresoras = $query
            ->orderBy('empresa_id')
            ->orderBy('nombre_asignado')
            ->paginate(20)
            ->withQueryString();

        return view('impresoras.index', [
            'impresoras' => $impresoras,
            'filtros'    => $this->filtrosActivos($request),
            'empresas'   => $this->empresasDisponiblesPara($user),
            'estados'    => Impresora::ESTADOS,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View|RedirectResponse
    {
        Gate::authorize('create', Impresora::class);

        $user = Auth::user();
        $empresas = $this->empresasDisponiblesPara($user);

        if ($empresas->isEmpty()) {
            return redirect()
                ->route('impresoras.index')
                ->with('error', 'No tenés empresas asignadas en tu alcance para crear impresoras.');
        }

        return view('impresoras.create', [
            'empresas'        => $empresas,
            'sedesPorEmpresa' => $this->sedesPorEmpresa($empresas->pluck('id')),
            'modelos'         => ImpresoraModelo::activos()
                ->orderBy('marca')->orderBy('modelo')
                ->get(['id', 'marca', 'modelo', 'tipo_tecnologia', 'color', 'es_multifuncional']),
            'estados'         => Impresora::ESTADOS,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreImpresoraRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Validar que la empresa elegida esté en el alcance
        $permitidas = $this->empresasDisponiblesPara($user)->pluck('id');

        if (! $permitidas->contains((int) $request->empresa_id)) {
            return back()
                ->withInput()
                ->withErrors(['empresa_id' => 'No tenés alcance sobre la empresa seleccionada.']);
        }

        // Validar que la sede pertenezca a la empresa (si se envió)
        if ($request->filled('sede_id')) {
            $sedeValida = Sede::where('id', $request->sede_id)
                ->where('empresa_id', $request->empresa_id)
                ->exists();

            if (! $sedeValida) {
                return back()
                    ->withInput()
                    ->withErrors(['sede_id' => 'La sede seleccionada no pertenece a esta empresa.']);
            }
        }

        $impresora = DB::transaction(function () use ($request, $user) {
            $impresora = Impresora::create(array_merge(
                $request->validated(),
                [
                    'codigo'      => Impresora::generarCodigo(),
                    'creado_por'  => $user->id,
                ]
            ));

            // Si tiene modelo del catálogo, generar los consumibles sugeridos
            if ($impresora->modelo_id && $impresora->modelo?->consumibles_sugeridos) {
                foreach ($impresora->modelo->consumibles_sugeridos as $sug) {
                    ImpresoraConsumible::create([
                        'impresora_id'   => $impresora->id,
                        'tipo'           => $sug['tipo'],
                        'color'          => $sug['color'] ?? 'no_aplica',
                        'dias_estimados' => 30, // default, el usuario ajusta luego
                        'activo'         => true,
                        'registrado_por' => $user->id,
                    ]);
                }
            }

            return $impresora;
        });

        return redirect()
            ->route('impresoras.show', $impresora)
            ->with('exito', "Impresora {$impresora->codigo} creada.");
    }

    public function show(Impresora $impresora): View
    {
        abort_unless(Auth::user()->can('impresoras.ver'), 403);
        Gate::authorize('view', $impresora);

        $impresora->load([
            'modelo',
            'empresa:id,nombre',
            'sede:id,nombre,direccion',
            'creador:id,name',
            'consumibles.registrador:id,name',
            'mantenimientos.realizador:id,name',
            'mantenimientos.registrador:id,name',
        ]);

        $consumiblesActivos = $impresora->consumibles
            ->where('activo', true)
            ->sortBy(fn ($c) => [$c->tipo, $c->color]);

        $historialConsumibles = $impresora->consumibles
            ->where('activo', false)
            ->sortByDesc('fecha_reemplazo');

        return view('impresoras.show', [
            'impresora'            => $impresora,
            'consumiblesActivos'   => $consumiblesActivos,
            'historialConsumibles' => $historialConsumibles,
            'mantenimientos'       => $impresora->mantenimientos,
            'tiposConsumible'      => ImpresoraConsumible::TIPOS,
            'coloresConsumible'    => ImpresoraConsumible::COLORES,
            'tiposMantenimiento'   => \App\Models\ImpresoraMantenimiento::TIPOS,
            'usuariosParaRealizar' => \App\Models\User::activos()
                ->orderBy('name')
                ->get(['id', 'name']),
            'puede'                => [
                'editar'                    => Auth::user()->can('update', $impresora),
                'eliminar'                  => Auth::user()->can('delete', $impresora),
                'mantener'                  => Auth::user()->can('mantener', $impresora),
                'gestionar_consumibles'     => Auth::user()->can('gestionarConsumibles', $impresora),
                'gestionar_mantenimientos'  => Auth::user()->can('gestionarMantenimientos', $impresora),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Impresora $impresora): View
    {
        Gate::authorize('update', $impresora);

        $user = Auth::user();
        $empresas = $this->empresasDisponiblesPara($user);

        return view('impresoras.edit', [
            'impresora'       => $impresora,
            'empresas'        => $empresas,
            'sedesPorEmpresa' => $this->sedesPorEmpresa($empresas->pluck('id')),
            'modelos'         => ImpresoraModelo::activos()
                ->orderBy('marca')->orderBy('modelo')
                ->get(['id', 'marca', 'modelo', 'tipo_tecnologia', 'color', 'es_multifuncional']),
            'estados'         => Impresora::ESTADOS,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateImpresoraRequest $request, Impresora $impresora): RedirectResponse
    {
        $impresora->update($request->validated());

        return redirect()
            ->route('impresoras.show', $impresora)
            ->with('exito', "Impresora {$impresora->codigo} actualizada.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Impresora $impresora): RedirectResponse
    {
        Gate::authorize('delete', $impresora);

        $codigo = $impresora->codigo;
        $impresora->delete();

        return redirect()
            ->route('impresoras.index')
            ->with('exito', "Impresora {$codigo} eliminada.");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers privados
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Empresas que este usuario puede ver, según su clasificación de rol.
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

            return Empresa::activas()->whereIn('id', $ids)->orderBy('nombre')->get(['id', 'nombre']);
        }

        if (Roles::usuarioCumple($user, 'requiereEmpresaPrincipal') && $user->empresa_id) {
            return Empresa::where('id', $user->empresa_id)->get(['id', 'nombre']);
        }

        return collect();
    }

    private function sedesPorEmpresa($idsEmpresas)
    {
        return Sede::activas()
            ->whereIn('empresa_id', $idsEmpresas)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'empresa_id'])
            ->groupBy('empresa_id');
    }

    /**
     * Restringe el listado a impresoras dentro del alcance del usuario.
     */
    private function scopePorAlcance(Builder $query, User $user): void
    {
        if (Roles::usuarioCumple($user, 'esTransversal')) {
            return;
        }

        if (Roles::usuarioCumple($user, 'requiereCobertura')) {
            $ids = DB::table('empresa_user')
                ->where('user_id', $user->id)
                ->distinct()
                ->pluck('empresa_id');

            $query->whereIn('empresa_id', $ids);
            return;
        }

        if (Roles::usuarioCumple($user, 'requiereEmpresaPrincipal') && $user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
            return;
        }

        // Sin alcance: fuerza resultado vacío
        $query->whereRaw('1 = 0');
    }

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('empresa_id'), fn($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->filled('sede_id'),    fn($q) => $q->where('sede_id', $request->sede_id))
            ->when($request->filled('estado'),     fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('modelo_id'),  fn($q) => $q->where('modelo_id', $request->modelo_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(
                    fn($sub) => $sub
                        ->where('codigo', 'like', $term)
                        ->orWhere('codigo_interno', 'like', $term)
                        ->orWhere('nombre_asignado', 'like', $term)
                        ->orWhere('ubicacion_fisica', 'like', $term)
                        ->orWhere('ip_red', 'like', $term)
                        ->orWhere('hostname', 'like', $term)
                        ->orWhere('numero_serie', 'like', $term)
                );
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['empresa_id', 'sede_id', 'estado', 'modelo_id', 'q']))
            ->filter(fn($v) => $v !== null && $v !== '')
            ->toArray();
    }
}
