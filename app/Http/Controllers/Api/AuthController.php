<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    /**
     * User login API method
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
            // Validación de entrada
        $credentials = $request->input('credentials'); // Obtener el objeto 'credentials' del request

        // Validar que el usuario y la contraseña se hayan proporcionado
        $validator = Validator::make($credentials, [
            'user' => 'required|string',
            'pass' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos no válidos',
                'details' => $validator->errors(),
            ], 422);
        }

        // Intentar obtener el usuario por nombre de usuario (user)
        $user = User::where('username', $credentials['user'])->first();

        if (!$user) {
            // Si el usuario no existe, devolver un mensaje claro
            return response()->json([
                'success' => false,
                'message' => 'El usuario no existe'
            ]);
        }

        if (!Hash::check($request->credentials['pass'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña Incorrecta'
            ]);
        }


        $tokenResult = $user->createToken('authToken');
        $user->access_token = $tokenResult->accessToken;
        $user->token_type = 'Bearer';
        $user->token_options = $tokenResult->token;
        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Login successfully'
        ]);
    }


      /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::user());
    }


    /**
     * User registration API method
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'username' => 'required|string|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            //return response()->json($validator->errors()->toJson(), 400);
            return response()->json([
                'message' => '¡Error al registrar al usuario!',
                'error' => $validator->errors()->toJson(),
            ]);
        }

        // Crear el usuario y generar el fullname automáticamente
        $usuario = new User;

        $usuario->nombres = $request->nombres;
        $usuario->apellidos = $request->apellidos;
        $usuario->fullname = $request->nombres . ' ' . $request->apellidos; // Concatenar nombres y apellidos
        $usuario->email = $request->email;
        $usuario->username = $request->username;
        $usuario->password = bcrypt($request->password);

        $usuario->save();
        // $user = User::create([
        //     'nombres' => $request->nombres,
        //     'apellidos' => $request->apellidos,
        //     'fullname' => $request->nombres . ' ' . $request->apellidos, // Concatenar nombres y apellidos
        //     'email' => $request->email,
        //     'username' => $request->username,
        //     'password' => bcrypt($request->password),
        // ]);
        // dd($request->all()); // Para inspeccionar los datos que llegan
        // Retornar una respuesta exitosa
        return response()->json([
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $usuario
        ]);
    }
}
