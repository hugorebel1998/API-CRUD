<?php

namespace App\Http\Controllers\V1\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listar(int $usuario_id = null)
    {
        if (empty($usuario_id))
            return User::all();
        return User::findOrFail($usuario_id);
    }

    public function crear(Request $request)
    {
        $inputs = $this->validate($request, [
            'nombre' => 'required',
            'apellidos' => 'required',
            'correo_electronico' => 'required|email|unique:users,correo_electronico',
            'celular' => 'required|min:10|max:10',
            'fotografia' => 'required|mimes:jpeg,jpg,png|max:10000',
            'contrasena' => 'required',
            'tipo_usuario'  => 'required'
        ]);

        // Se obtiene los roles
        $tipo_usuario = Role::where('nombre', 'like', '%' . $inputs['tipo_usuario'] . '%')->pluck('id')->first();

        if (!$tipo_usuario)
            return response()->json(['success' => false, 'message' => 'El tipo de usuario no exite'], 404);

        if ($archivo = $request->file('fotografia')) {
            $nombre_imagen = $archivo->getClientOriginalName();
            $ruta = public_path('img/products/');
            $archivo->move($ruta, $nombre_imagen);
            $inputs['fotografia'] = $nombre_imagen;
        }

        $usuario = User::create([
            'nombre' => $inputs['nombre'],
            'apellidos' => $inputs['apellidos'],
            'correo_electronico' => $inputs['correo_electronico'],
            'celular' => $inputs['celular'],
            'fotografia' => isset($inputs['fotografia']) ? $inputs['fotografia'] : null,
            'contrasena' => $inputs['contrasena'],
            'rol_id' => $tipo_usuario
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado con éxito',
            'data' => $usuario
        ], 200);
    }

    public function update(Request $request, $usuario_id)
    {
        $usuario = User::findOrFail($usuario_id);

        $inputs = $this->validate($request, [
            'nombre' => 'sometimes',
            'apellidos' => 'sometimes',
            'correo_electronico' => 'sometimes|email|unique:users,correo_electronico,' . $usuario->id,
            'celular' => 'sometimes',
            'fotografia' => 'sometimes|mimes:jpeg,jpg,png|max:10000',
            'contrasena' => 'sometimes',
            'tipo_usuario'  => 'sometimes'
        ]);

        //Se obtiene los roles
        $tipo_usuario = Role::where('nombre', 'like', '%' . $inputs['tipo_usuario'] . '%')->pluck('id')->first();

        if (!$tipo_usuario)
            return response()->json(['success' => false, 'message' => 'El tipo de usuario no exite'], 404);

        if ($archivo = $request->file('fotografia')) {
            $nombre_imagen = $archivo->getClientOriginalName();
            $ruta = public_path('img/products/');
            $archivo->move($ruta, $nombre_imagen);
            $inputs['fotografia'] = $nombre_imagen;
        }

        $usuario->update([
            'nombre' => isset($inputs['nombre']) ? $inputs['nombre'] : $usuario['nombre'],
            'apellidos' => isset($inputs['apellidos']) ? $inputs['apellidos'] : $usuario['apellidos'],
            'correo_electronico' => isset($inputs['correo_electronico']) ? $inputs['correo_electronico'] : $usuario['correo_electronico'],
            'celular' => isset($inputs['celular']) ? $inputs['celular'] : $usuario,
            'fotografia' =>  isset($inputs['fotografia']) ? $inputs['fotografia'] : $usuario['fotografia'],
            'contrasena' => isset($inputs['contrasena']) ? $inputs['contrasena'] : $usuario['contrasena'],
            'rol_id' => isset($tipo_usuario) ? $tipo_usuario : $usuario['rol_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado con éxito',
            'data' => $usuario
        ], 200);
    }

    public function delete($usuario_id)
    {
        $usuario = User::findOrFail($usuario_id);
        $usuario->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado con éxito'], 200);
    }

    public function restablecer($usuario_id)
    {
        $usuario = User::find($usuario_id);
        User::onlyTrashed()->findOrFail($usuario_id)->restore();

        return response()->json(['success' => true, 'message' => 'Usuario se restablecio con éxito'], 200);
    }
}
