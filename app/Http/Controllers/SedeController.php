<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SedeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Listado de sedes con filtros, conteos y carga ansiosa de empresa.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        // Iniciamos la query cargando la relación empresa para evitar el N+1
        $query = Sede::query()->with('empresa:id,nombre');

        $this->aplicarFiltros($query, $request);

        $sedes = $query
            ->withCount([
                'usuarios',
                'tickets as tickets_activos_count' => fn($q) => $q->whereNotIn('estado', ['cerrado', 'cancelado']),
            ])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('sedes.index', [
            'sedes'    => $sedes,
            'filtros'  => $this->filtrosActivos($request),
            'totales'  => $this->resumen(),
            // Necesitamos las empresas activas para los select de los modales y los filtros
            'empresas' => Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        $data = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'nombre'     => ['required', 'string', 'max:255'],
            'ciudad'     => ['nullable', 'string', 'max:255'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'telefono'   => ['nullable', 'string', 'max:50'],
        ]);

        $data['activo'] = $request->has('activo') ? $request->boolean('activo') : true;

        $sede = Sede::create($data);

        // back() devuelve al usuario a la URL exacta desde donde envió el formulario
        return back()->with('success', "La sede {$sede->nombre} fue registrada correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Sede $sede): RedirectResponse
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        $data = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'nombre'     => ['required', 'string', 'max:255'],
            'ciudad'     => ['nullable', 'string', 'max:255'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'telefono'   => ['nullable', 'string', 'max:50'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $sede->update($data);

        return redirect()->route('sedes.index')
            ->with('success', "La sede {$sede->nombre} fue actualizada correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Sede $sede): RedirectResponse
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        // Protección contra errores de llave foránea (Integridad Referencial)
        if ($sede->usuarios()->exists() || $sede->tickets()->exists()) {
            return back()->with('error', 'No puedes eliminar esta sede porque tiene usuarios o tickets asociados. Te recomendamos desactivarla.');
        }

        $nombre = $sede->nombre;
        $sede->delete();

        return redirect()->route('sedes.index')
            ->with('success', "La sede {$nombre} fue eliminada permanentemente.");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers privados
    // ─────────────────────────────────────────────────────────────────────

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->boolean('solo_activas'), fn($q) => $q->where('activo', true))
            ->when($request->filled('empresa_id'), fn($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn($sub) => $sub
                    ->where('nombre', 'like', $term)
                    ->orWhere('ciudad', 'like', $term)
                    ->orWhere('direccion', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['q', 'empresa_id', 'solo_activas']))
            ->filter(fn($v) => $v !== null && $v !== '' && $v !== '0')
            ->toArray();
    }

    /**
     * Resumen ligero para mostrar en el encabezado de la vista.
     */
    private function resumen(): array
    {
        return [
            'total'       => Sede::count(),
            'activas'     => Sede::activas()->count(),
            'sinUsuarios' => Sede::doesntHave('usuarios')->count(),
        ];
    }
}
