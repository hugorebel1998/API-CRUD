<?php

namespace App\Http\Controllers\V1\Permission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function listar(int $permiso_id = null)
    {
        if (empty($permiso_id))
            return Permission::all();

        return Permission::findOrFail($permiso_id);
    }

    public function crear(Request $request)
    {
        $inputs = $this->validate($request, [
            'nombre' => 'required|unique:permissions,nombre',
        ]);

        $permiso = Permission::create([
            'nombre' => strtolower($inputs['nombre']),
            'status' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nuevo permiso creado',
            'data' => $permiso
        ], 200);
    }
}