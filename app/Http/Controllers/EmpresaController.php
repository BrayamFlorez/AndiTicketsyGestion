<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Listado de empresas con filtros, conteos y lista de migración.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $query = Empresa::query();

        $this->aplicarFiltros($query, $request);

        $empresas = $query
            ->with(['sedes'])
            ->withCount([
                'sedes',
                'usuarios',
                'equipos',
                'impresoras',
                'tickets as tickets_activos_count' => fn($q) => $q->whereNotIn('estado', ['cerrado', 'cancelado']),
            ])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('empresas.index', [
            'empresas'      => $empresas,
            'todasEmpresas' => Empresa::orderBy('nombre')->get(['id', 'nombre']), // Para selector de migración
            'filtros'       => $this->filtrosActivos($request),
            'totales'       => $this->resumen(),
        ]);
    }

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

        $data['activo'] = $request->has('activo') ? $request->boolean('activo') : true;

        $empresa = Empresa::create($data);

        return redirect()->route('empresas.index')
            ->with('success', "La empresa {$empresa->nombre} fue registrada correctamente.");
    }

    public function show(Request $request, Empresa $empresa): View
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $empresa->load([
            'sedes' => fn($q) => $q->withCount([
                'usuarios',
                'tickets as tickets_activos_count' => fn($sub) => $sub->whereNotIn('estado', ['cerrado', 'cancelado'])
            ])->orderBy('nombre')
        ])->loadCount(['sedes', 'usuarios', 'tickets']);

        return view('empresas.show', compact('empresa'));
    }

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

        $data['activo'] = $request->boolean('activo');

        $empresa->update($data);

        return redirect()->route('empresas.index')
            ->with('success', "La empresa {$empresa->nombre} fue actualizada correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY (CON SISTEMA DE MIGRACIÓN GLOBAL)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Empresa $empresa): RedirectResponse
    {
        abort_unless($request->user()->can('empresas.gestionar'), 403);

        $migrarA = $request->input('migrar_a_empresa_id');
        $nombreEmpresa = $empresa->nombre;

        if ($migrarA) {
            $empresaDestino = Empresa::findOrFail($migrarA);

            DB::transaction(function () use ($empresa, $empresaDestino) {
                // Reasignación masiva de relaciones a la nueva Empresa
                if (method_exists($empresa, 'sedes')) {
                    $empresa->sedes()->update(['empresa_id' => $empresaDestino->id]);
                }
                if (method_exists($empresa, 'usuarios')) {
                    $empresa->usuarios()->update(['empresa_id' => $empresaDestino->id]);
                }
                if (method_exists($empresa, 'equipos')) {
                    $empresa->equipos()->update(['empresa_id' => $empresaDestino->id]);
                }
                if (method_exists($empresa, 'impresoras')) {
                    $empresa->impresoras()->update(['empresa_id' => $empresaDestino->id]);
                }
                if (method_exists($empresa, 'tickets')) {
                    $empresa->tickets()->update(['empresa_id' => $empresaDestino->id]);
                }
                
                // Reasignación en tabla pivote de cobertura técnica
                DB::table('empresa_user')
                    ->where('empresa_id', $empresa->id)
                    ->update(['empresa_id' => $empresaDestino->id]);
            });

            $mensaje = "La empresa '{$nombreEmpresa}' fue eliminada. Todos sus recursos y sedes fueron migrados a '{$empresaDestino->nombre}'.";
        } else {
            $tieneDependencias = 
                $empresa->sedes()->exists() || 
                $empresa->usuarios()->exists() || 
                $empresa->equipos()->exists() || 
                $empresa->impresoras()->exists() || 
                $empresa->tickets()->exists();

            if ($tieneDependencias) {
                return back()->with('error', 'La empresa contiene recursos activos. Debes seleccionar una empresa de destino para migrar los datos antes de eliminarla.');
            }
            $mensaje = "La empresa '{$nombreEmpresa}' fue eliminada permanentemente.";
        }

        $empresa->delete();

        return redirect()->route('empresas.index')->with('success', $mensaje);
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

    private function resumen(): array
    {
        return [
            'total'    => Empresa::count(),
            'activas'  => Empresa::where('activo', true)->count(),
            'sinSedes' => Empresa::doesntHave('sedes')->count(),
        ];
    }
}