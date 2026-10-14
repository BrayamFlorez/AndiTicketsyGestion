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
    | INDEX 
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        $query = Sede::query()->with('empresa:id,nombre');

        $this->aplicarFiltros($query, $request);

        $sedes = $query
            ->withCount([
                'usuarios',
                'equipos',
                'impresoras', // ← Agregado para el conteo exacto de dependencias
                'tickets as tickets_activos_count' => fn($q) => $q->whereNotIn('estado', ['cerrado', 'cancelado']),
            ])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('sedes.index', [
            'sedes'    => $sedes,
            'filtros'  => $this->filtrosActivos($request),
            'totales'  => $this->resumen(),
            'empresas' => Empresa::activas()->with('sedes:id,nombre,empresa_id')->orderBy('nombre')->get(['id', 'nombre']),
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
    | DESTROY (CON MIGRACIÓN DE IMPRESORAS, EQUIPOS, USUARIOS Y TICKETS)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Sede $sede): RedirectResponse
    {
        abort_unless($request->user()->can('sedes.gestionar'), 403);

        $migrarA = $request->input('migrar_a_sede_id');
        $nombreSede = $sede->nombre;

        if ($migrarA) {
            $sedeDestino = Sede::findOrFail($migrarA);
            
            // Reasignación masiva de dependencias a la nueva sede
            if (method_exists($sede, 'usuarios')) {
                $sede->usuarios()->update(['sede_id' => $sedeDestino->id]);
            }
            if (method_exists($sede, 'equipos')) {
                $sede->equipos()->update(['sede_id' => $sedeDestino->id]);
            }
            if (method_exists($sede, 'impresoras')) {
                $sede->impresoras()->update(['sede_id' => $sedeDestino->id]); // ← Reasignación de Impresoras
            }
            if (method_exists($sede, 'tickets')) {
                $sede->tickets()->update(['sede_id' => $sedeDestino->id]);
            }
            
            $mensaje = "La sede {$nombreSede} fue eliminada. Todo su hardware, impresoras y usuarios se migraron a '{$sedeDestino->nombre}'.";
        } else {
            // Verificación de integridad si no se especifica sede de migración
            $tieneDependencias = 
                (method_exists($sede, 'usuarios') && $sede->usuarios()->exists()) ||
                (method_exists($sede, 'equipos') && $sede->equipos()->exists()) ||
                (method_exists($sede, 'impresoras') && $sede->impresoras()->exists()) || // ← Validación Impresoras
                (method_exists($sede, 'tickets') && $sede->tickets()->exists());

            if ($tieneDependencias) {
                return back()->with('error', 'La sede tiene registros asociados (equipos, impresoras, usuarios o tickets). Selecciona una sede de destino para migrar los datos antes de eliminarla.');
            }
            $mensaje = "La sede {$nombreSede} fue eliminada permanentemente.";
        }

        $sede->delete();

        return redirect()->route('sedes.index')->with('success', $mensaje);
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

    private function resumen(): array
    {
        return [
            'total'       => Sede::count(),
            'activas'     => Sede::activas()->count(),
            'sinUsuarios' => Sede::doesntHave('usuarios')->count(),
        ];
    }
}