<?php

namespace App\Http\Controllers\V1\Roles;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function listar(int $role = null)
    {
        if (empty($role))
            return Role::all();

        return Role::findOrFail($role);
    }

    public function crear(Request $request)
    {
        $inputs = $this->validate($request, [
            'nombre' => 'required|unique:roles,nombre',
        ]);

        $role = Role::create([
            'nombre' => ucfirst($inputs['nombre']),
            'status' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nuevo rol creado',
            'data' => $role
        ], 200);
    }

    public function update(Request $request, $role_id)
    {
        $role = Role::findOrFail($role_id);

        $inputs = $this->validate($request, [
            'nombre' => 'sometimes',
            'permission_id' => 'sometimes',
        ]);


        $role->update([
            'nombre' =>  isset($inputs['nombre']) ? $inputs['nombre'] : $role['nombre'],
            'permission_id' => isset($inputs['permission_id']) ? $inputs['permission_id'] : $role['permission_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado con éxito',
            'data' => $role
        ], 200);
    }

    public function asignarPermiso(Request $request, $role_id)
    {
        $role = Role::findOrFail($role_id);

        // Obtener los permisos seleccionados desde la solicitud
        $selectedPermissions = $request->input('permiso', []);

        // Obtener los modelos de permisos asociados a los ID seleccionados
        $permissions = Permission::where('nombre', $selectedPermissions)->get();

        // Asignar permisos al rol
        $role->permissions()->attach($permissions);

        return response()->json(['message' => 'Permisos asignados correctamente']);
    }
}
