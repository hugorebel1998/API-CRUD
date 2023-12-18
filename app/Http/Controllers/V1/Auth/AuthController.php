<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
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
            'contrasena' => Hash::make($inputs['contrasena']),
            'rol_id' => $tipo_usuario
        ]);

        $token = $usuario->createToken('auth_token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Nuevo registro creado',
            'data' => $usuario,
            'token' => $token
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $inputs = $this->validate($request, [
            'celular' => 'required|min:10|max:10',
            'contrasena' => 'required',
        ]);

        $usuario = User::where('celular', $inputs['celular'])->first();

        if (!$usuario || !Hash::check($inputs['contrasena'], $usuario['contrasena']))
            return response()->json(['success' => false, 'message' => 'Las credenciales son incorrectas']);

        $token = $usuario->createToken('auth_token')->accessToken;

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'token' => $token
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['success' => true, 'message' => 'Saliste de tu sesión'], 200);
    }
}
