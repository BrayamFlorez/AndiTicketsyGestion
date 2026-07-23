<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RoleController extends Controller
{

    public function index()
    {
        $roles = \Spatie\Permission\Models\Role::with('permissions', 'users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = \Spatie\Permission\Models\Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('status', 'rol-creado');
    }

    public function edit($id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);
        $permissions = \Spatie\Permission\Models\Permission::all();
        
        // Obtenemos solo los nombres de los permisos que ya tiene el rol
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        
        // Sincroniza los permisos (quita los que no están y pone los nuevos)
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('status', 'rol-actualizado');
    }

    public function destroy($id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);

        // Seguridad: Evitar borrar el rol administrador principal
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->with('error', 'El rol de administrador no puede ser eliminado.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'rol-eliminado');
    }
}