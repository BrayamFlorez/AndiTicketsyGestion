<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraModeloRequest;
use App\Http\Requests\UpdateImpresoraModeloRequest;
use App\Models\ImpresoraModelo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpresoraModeloController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('impresoras.gestionar-catalogo'), 403);

        $modelos = ImpresoraModelo::query()
            ->withCount('impresoras')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn ($sub) => $sub
                    ->where('marca', 'like', $term)
                    ->orWhere('modelo', 'like', $term));
            })
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo_tecnologia', $request->tipo))
            ->orderBy('marca')
            ->orderBy('modelo')
            ->paginate(20)
            ->withQueryString();

        return view('impresoras.catalogo.index', [
            'modelos'    => $modelos,
            'filtros'    => $request->only(['q', 'tipo']),
            'tecnologias' => ImpresoraModelo::TIPOS_TECNOLOGIA,
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('impresoras.gestionar-catalogo'), 403);

        return view('impresoras.catalogo.create', $this->datosFormulario());
    }

    public function store(StoreImpresoraModeloRequest $request): RedirectResponse
    {
        ImpresoraModelo::create([
            'marca'                 => $request->marca,
            'modelo'                => $request->modelo,
            'tipo_tecnologia'       => $request->tipo_tecnologia,
            'color'                 => $request->boolean('color'),
            'es_multifuncional'     => $request->boolean('es_multifuncional'),
            'activo'                => $request->boolean('activo', true),
            'consumibles_sugeridos' => $this->normalizarConsumibles($request->input('consumibles', [])),
        ]);

        return redirect()
            ->route('impresoras.catalogo.index')
            ->with('exito', "Modelo {$request->marca} {$request->modelo} agregado al catálogo.");
    }

    public function edit(ImpresoraModelo $modelo): View
    {
        abort_unless(auth()->user()->can('impresoras.gestionar-catalogo'), 403);

        return view('impresoras.catalogo.edit', array_merge(
            $this->datosFormulario(),
            ['modelo' => $modelo]
        ));
    }

    public function update(UpdateImpresoraModeloRequest $request, ImpresoraModelo $modelo): RedirectResponse
    {
        $modelo->update([
            'marca'                 => $request->marca,
            'modelo'                => $request->modelo,
            'tipo_tecnologia'       => $request->tipo_tecnologia,
            'color'                 => $request->boolean('color'),
            'es_multifuncional'     => $request->boolean('es_multifuncional'),
            'activo'                => $request->boolean('activo'),
            'consumibles_sugeridos' => $this->normalizarConsumibles($request->input('consumibles', [])),
        ]);

        return redirect()
            ->route('impresoras.catalogo.index')
            ->with('exito', "Modelo {$modelo->nombre_completo} actualizado.");
    }

    /**
     * Toggle rápido de activo/inactivo desde el listado.
     */
    public function toggleActivo(Request $request, ImpresoraModelo $modelo): RedirectResponse
    {
        abort_unless($request->user()->can('impresoras.gestionar-catalogo'), 403);

        $modelo->update(['activo' => ! $modelo->activo]);

        $estado = $modelo->activo ? 'activado' : 'desactivado';
        return back()->with('exito', "Modelo {$estado}.");
    }

    public function destroy(Request $request, ImpresoraModelo $modelo): RedirectResponse
    {
        abort_unless($request->user()->can('impresoras.gestionar-catalogo'), 403);

        // Bloquear eliminación si hay impresoras usando este modelo
        if ($modelo->impresoras()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar: hay impresoras registradas con este modelo. Desactivalo si no querés que aparezca al crear nuevas.'
            );
        }

        $nombre = $modelo->nombre_completo;
        $modelo->delete();

        return redirect()
            ->route('impresoras.catalogo.index')
            ->with('exito', "Modelo {$nombre} eliminado del catálogo.");
    }

    // ─────────────────────────────────────────────────────────────────────

    private function datosFormulario(): array
    {
        return [
            'tecnologias'       => ImpresoraModelo::TIPOS_TECNOLOGIA,
            'tiposConsumible'   => \App\Models\ImpresoraConsumible::TIPOS,
            'coloresConsumible' => \App\Models\ImpresoraConsumible::COLORES,
        ];
    }

    /**
     * Limpia y normaliza el array de consumibles antes de guardarlo como JSON.
     * Descarta filas vacías y deduplica por tipo+color.
     */
    private function normalizarConsumibles(array $consumibles): array
    {
        return collect($consumibles)
            ->filter(fn ($c) => ! empty($c['tipo']))
            ->map(fn ($c) => [
                'tipo'  => $c['tipo'],
                'color' => $c['color'] ?? 'no_aplica',
            ])
            ->unique(fn ($c) => $c['tipo'] . '|' . $c['color'])
            ->values()
            ->all();
    }
}