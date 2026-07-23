<?php

namespace App\Http\Controllers;

use App\Actions\Flujos\GuardarPlantillaAction;
use App\Http\Requests\Flujos\StoreWorkflowTemplateRequest;
use App\Http\Requests\Flujos\UpdateWorkflowTemplateRequest;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class WorkflowTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only(['q', 'activo']);

        $plantillas = WorkflowTemplate::query()
            ->withCount(['stages', 'instances', 'documentTypes'])
            ->when($filtros['q'] ?? null, fn ($q, $t) =>
                $q->where('nombre', 'like', "%{$t}%")
                  ->orWhere('descripcion', 'like', "%{$t}%")
            )
            ->when(
                array_key_exists('activo', $filtros) && $filtros['activo'] !== '',
                fn ($q) => $q->where('activo', (bool) $filtros['activo'])
            )
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('flujos.plantillas.index', compact('plantillas', 'filtros'));
    }

    public function create(): View
    {
        $plantilla = new WorkflowTemplate(['activo' => true]);
        $plantilla->setRelation('stages', collect());

        return view('flujos.plantillas.edit', [
            'plantilla'      => $plantilla,
            'roles'          => $this->rolesDisponibles(),
            'bloqueada'      => false,
            'instanciasCount' => 0,
            'modo'           => 'create',
        ]);
    }

    public function store(StoreWorkflowTemplateRequest $request, GuardarPlantillaAction $accion): RedirectResponse
    {
        $plantilla = $accion->ejecutar(null, $request->validated(), $request->user());

        return redirect()
            ->route('flujos.plantillas.edit', $plantilla)
            ->with('exito', 'Plantilla creada correctamente.');
    }

    public function edit(WorkflowTemplate $plantilla): View
    {
        $plantilla->load(['stages.actors']);
        $instanciasCount = $plantilla->instances()->count();

        return view('flujos.plantillas.edit', [
            'plantilla'       => $plantilla,
            'roles'           => $this->rolesDisponibles(),
            'bloqueada'       => $instanciasCount > 0,
            'instanciasCount' => $instanciasCount,
            'modo'            => 'edit',
        ]);
    }

    public function update(UpdateWorkflowTemplateRequest $request, WorkflowTemplate $plantilla, GuardarPlantillaAction $accion): RedirectResponse
    {
        $accion->ejecutar($plantilla, $request->validated(), $request->user());

        return redirect()
            ->route('flujos.plantillas.edit', $plantilla)
            ->with('exito', 'Plantilla actualizada.');
    }

    public function destroy(WorkflowTemplate $plantilla): RedirectResponse
    {
        if ($plantilla->instances()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar: la plantilla ya tiene instancias registradas. Puede desactivarla en su lugar.'
            );
        }

        $plantilla->delete();

        return redirect()
            ->route('flujos.plantillas.index')
            ->with('exito', 'Plantilla eliminada.');
    }

    public function toggleActivo(WorkflowTemplate $plantilla): RedirectResponse
    {
        $plantilla->update(['activo' => ! $plantilla->activo]);

        return back()->with(
            'exito',
            $plantilla->activo ? 'Plantilla activada.' : 'Plantilla desactivada.'
        );
    }

    /**
     * Endpoint JSON: buscador de usuarios para el picker de actores.
     */
    public function buscarUsuarios(Request $request): JsonResponse
    {
        $texto = trim((string) $request->query('q', ''));

        if (mb_strlen($texto) < 2) {
            return response()->json([]);
        }

        $usuarios = User::query()
            ->where(fn ($q) => $q->where('name', 'like', "%{$texto}%")
                                 ->orWhere('email', 'like', "%{$texto}%"))
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($usuarios);
    }

    /**
     * Roles de Spatie disponibles para el picker de actores.
     */
    private function rolesDisponibles()
    {
        return Role::orderBy('name')->get(['id', 'name']);
    }
}