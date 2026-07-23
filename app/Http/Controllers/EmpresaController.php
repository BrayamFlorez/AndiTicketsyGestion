<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Listado de empresas con filtros y conteos útiles.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $query = Empresa::query();

        $this->aplicarFiltros($query, $request);

        $empresas = $query
            ->with(['sedes']) // <-- ¡NUEVO! Carga las sedes para el acordeón
            ->withCount([
                'sedes',
                'usuarios',
                'tickets as tickets_activos_count' => fn($q) => $q->whereNotIn('estado', ['cerrado', 'cancelado']),
            ])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('empresas.index', [
            'empresas' => $empresas,
            'filtros'  => $this->filtrosActivos($request),
            'totales'  => $this->resumen(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / STORE
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        return view('empresas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'nit'      => ['nullable', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email'    => ['nullable', 'email', 'max:255'],
        ]);

        // Al crear, forzamos que esté activa o tomamos el valor del checkbox si lo envías
        $data['activo'] = $request->has('activo') ? $request->boolean('activo') : true;

        $empresa = Empresa::create($data);

        return redirect()->route('empresas.index')
            ->with('success', "La empresa {$empresa->nombre} fue registrada correctamente.");
    }

    public function show(Request $request, Empresa $empresa): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        // Cargamos las sedes de la empresa calculando sus usuarios y tickets activos individuales
        $empresa->load([
            'sedes' => fn($q) => $q->withCount([
                'usuarios',
                'tickets as tickets_activos_count' => fn($sub) => $sub->whereNotIn('estado', ['cerrado', 'cancelado'])
            ])->orderBy('nombre')
        ])->loadCount(['sedes', 'usuarios', 'tickets']);

        return view('empresas.show', compact('empresa'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT / UPDATE
    |--------------------------------------------------------------------------
    */
    public function edit(Request $request, Empresa $empresa): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'nit'      => ['nullable', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email'    => ['nullable', 'email', 'max:255'],
        ]);

        // Checkbox HTML: si no se marca, no viaja en el request. Usamos boolean().
        $data['activo'] = $request->boolean('activo');

        $empresa->update($data);

        return redirect()->route('empresas.index')
            ->with('success', "La empresa {$empresa->nombre} fue actualizada correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Empresa $empresa): RedirectResponse
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        // Protección contra errores de llave foránea (Integridad Referencial)
        if ($empresa->sedes()->exists() || $empresa->usuarios()->exists() || $empresa->tickets()->exists()) {
            return back()->with('error', 'No puedes eliminar esta empresa porque tiene sedes, usuarios o tickets asociados. Te recomendamos desactivarla.');
        }

        $nombre = $empresa->nombre;
        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', "La empresa {$nombre} fue eliminada permanentemente.");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers privados
    // ─────────────────────────────────────────────────────────────────────

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->boolean('solo_activas'), fn($q) => $q->where('activo', true))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn($sub) => $sub
                    ->where('nombre', 'like', $term)
                    ->orWhere('nit', 'like', $term)
                    ->orWhere('email', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['q', 'solo_activas']))
            ->filter(fn($v) => $v !== null && $v !== '' && $v !== '0')
            ->toArray();
    }

    /**
     * Resumen ligero para mostrar en el encabezado de la vista.
     */
    private function resumen(): array
    {
        return [
            'total'    => Empresa::count(),
            'activas'  => Empresa::where('activo', true)->count(),
            'sinSedes' => Empresa::doesntHave('sedes')->count(),
        ];
    }
}