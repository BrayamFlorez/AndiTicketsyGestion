<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowStepAssignment;
use App\Support\Roles as RolesSupport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Roles del sistema que nunca deben eliminarse.
     * Podés reordenar según tu criterio.
     */
    private const ROLES_PROTEGIDOS = ['super-admin', 'admin'];

    public function index(): View
    {
        $roles = Role::with('permissions', 'users')->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('status', 'rol-creado');
    }

    public function edit($id): View
    {
        $role            = Role::findOrFail($id);
        $permissions     = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('status', 'rol-actualizado');
    }

    /**
     * Muestra pantalla de confirmación con análisis de impacto.
     * Si hay dependencias, obliga a elegir un rol reemplazo.
     */
    public function confirmarEliminacion(Role $role): View|RedirectResponse
    {
        if (in_array($role->name, self::ROLES_PROTEGIDOS, true)) {
            return redirect()->route('roles.index')
                ->with('error', "El rol '{$role->name}' está protegido y no puede ser eliminado.");
        }

        $impacto = $this->calcularImpacto($role);

        // Roles alternativos para reemplazo (excluye el que se está eliminando)
        $rolesAlternativos = Role::where('id', '!=', $role->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('roles.confirmar-eliminacion', compact('role', 'impacto', 'rolesAlternativos'));
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, self::ROLES_PROTEGIDOS, true)) {
            return redirect()->route('roles.index')
                ->with('error', "El rol '{$role->name}' está protegido y no puede ser eliminado.");
        }

        $impacto = $this->calcularImpacto($role);

        // Sin dependencias: eliminar directo
        if (! $impacto['tiene_dependencias']) {
            $role->delete();
            return redirect()->route('roles.index')->with('status', 'rol-eliminado');
        }

        // Con dependencias: exigir rol reemplazo
        $data = $request->validate([
            'rol_reemplazo_id' => [
                'required',
                'integer',
                'different:' . $role->id,
                'exists:roles,id',
            ],
        ], [
            'rol_reemplazo_id.required'  => 'Debes elegir un rol al que reasignar las responsabilidades.',
            'rol_reemplazo_id.different' => 'El rol reemplazo no puede ser el mismo que estás eliminando.',
            'rol_reemplazo_id.exists'    => 'El rol reemplazo elegido no existe.',
        ]);

        $rolReemplazo = Role::findOrFail($data['rol_reemplazo_id']);

        try {
            DB::transaction(function () use ($role, $rolReemplazo) {

                // 1. Reasignar usuarios que tenían este rol al rol reemplazo
                $usuarios = User::role($role->name)->get();
                foreach ($usuarios as $usuario) {
                    // syncRoles reemplaza TODOS los roles del usuario.
                    // Como acá queremos solo cambiar el afectado, hacemos remove + assign.
                    $usuario->removeRole($role->name);
                    if (! $usuario->hasRole($rolReemplazo->name)) {
                        $usuario->assignRole($rolReemplazo->name);
                    }
                }

                // 2. Reapuntar actores de plantillas de flujo al rol nuevo.
                //    Evita duplicados: si la etapa ya tenía al rol reemplazo, se salta.
                $actores = WorkflowStageActor::where('actor_type', WorkflowStageActor::TIPO_ROL)
                    ->where('actor_id', $role->id)
                    ->get();

                foreach ($actores as $actor) {
                    $yaExiste = WorkflowStageActor::where('workflow_stage_id', $actor->workflow_stage_id)
                        ->where('actor_type', WorkflowStageActor::TIPO_ROL)
                        ->where('actor_id', $rolReemplazo->id)
                        ->exists();

                    if ($yaExiste) {
                        // La etapa ya tenía al rol reemplazo como actor → borrar el viejo
                        $actor->delete();
                    } else {
                        $actor->update(['actor_id' => $rolReemplazo->id]);
                    }
                }

                // 3. Eliminar el rol
                //    Spatie limpia automáticamente model_has_roles y role_has_permissions.
                $role->delete();
            });
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('roles.index')
                ->with('error', 'No se pudo eliminar el rol: ' . $e->getMessage());
        }

        return redirect()->route('roles.index')->with('status', 'rol-reasignado-y-eliminado');
    }

    /**
     * Calcula el impacto de eliminar un rol.
     * Devuelve conteos y muestras para mostrar en la vista de confirmación.
     */
    private function calcularImpacto(Role $role): array
    {
        // Usuarios con el rol
        $usuarios = User::role($role->name)->get(['id', 'name', 'email']);

        // Plantillas donde el rol es actor de alguna etapa
        $plantillas = DB::table('workflow_stage_actors')
            ->join('workflow_stages', 'workflow_stages.id', '=', 'workflow_stage_actors.workflow_stage_id')
            ->join('workflow_templates', 'workflow_templates.id', '=', 'workflow_stages.workflow_template_id')
            ->where('workflow_stage_actors.actor_type', WorkflowStageActor::TIPO_ROL)
            ->where('workflow_stage_actors.actor_id', $role->id)
            ->select('workflow_templates.id', 'workflow_templates.nombre')
            ->distinct()
            ->get();

        // Asignaciones activas pendientes que dependen indirectamente del rol
        // (vía workflow_stage_actors)
        $asignacionesActivas = WorkflowStepAssignment::where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE)
            ->whereHas('stageActor', function ($q) use ($role) {
                $q->where('actor_type', WorkflowStageActor::TIPO_ROL)
                  ->where('actor_id', $role->id);
            })
            ->count();

        return [
            'usuarios'             => $usuarios,
            'usuarios_count'       => $usuarios->count(),
            'plantillas'           => $plantillas,
            'plantillas_count'     => $plantillas->count(),
            'asignaciones_activas' => $asignacionesActivas,
            'tiene_dependencias'   => $usuarios->isNotEmpty()
                                       || $plantillas->isNotEmpty()
                                       || $asignacionesActivas > 0,
        ];
    }
}