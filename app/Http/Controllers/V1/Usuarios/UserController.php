<?php

namespace App\Http\Controllers\V1\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param int|null $usuario_id
     * 
     * @return [type]
     */
    public function listar(int $usuario_id = null)
    {
        $user = Auth::user();

        if (!in_array($user->role->nombre, ['Administrador', 'Basico']))
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

        // El usuario tiene permisos, devolver la lista de usuarios
        if (empty($usuario_id))
            return User::all();
        return User::findOrFail($usuario_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {
        $user = Auth::user();

        if ($user->role->nombre !== 'Administrador')
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *  @param mixed $usuario_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $usuario_id)
    {
        $user = Auth::user();

        if (!in_array($user->role->nombre, ['Administrador', 'Basico']))
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $usuario
     * @return \Illuminate\Http\Response
     */
    public function delete($usuario_id)
    {
        $user = Auth::user();

        if ($user->role->nombre !== 'Administrador')
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

        $usuario = User::findOrFail($usuario_id);
        $usuario->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado con éxito'], 200);
    }

    /**
     * Reset the specified resource from storage.
     *
     * @param  int  $usuario
     * @return \Illuminate\Http\Response
     */
    public function restablecer($usuario_id)
    {
        $user = Auth::user();

        if ($user->role->nombre !== 'Administrador')
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

        $usuario = User::find($usuario_id);
        User::onlyTrashed()->findOrFail($usuario_id)->restore();

        return response()->json(['success' => true, 'message' => 'Usuario se restablecio con éxito'], 200);
    }

    /**
     * Generate a report
     * @return [type]
     */
    public function reporte()
    {
        $user = Auth::user();

        if ($user->role->nombre !== 'Administrador')
            return response()->json(['success' => false, 'message' => 'No tienes los permisos necesarios'], 400);

        $usuarios = User::all();

        $filePath = storage_path('app/reportes/reportes-usuarios.pdf');

        File::makeDirectory(dirname($filePath), 0777, true, true);

        $pdf = PDF::loadView('reporte_pdf', ['usuarios' => $usuarios]);
        $pdf->save($filePath);

        $url = url('/storage/reportes/reportes-usuarios.pdf');
        return response()->json(['mensaje' => 'El reporte ha sido generado y se encuentra disponible en: ' . $url]);
    }
}
