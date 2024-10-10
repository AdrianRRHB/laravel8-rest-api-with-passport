<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function getUsuarios(Request $request)
    {
        $search = '%' . $request->search . '%';
        $user = User::where([["estado", '=', '0'], ['fullname', 'like', $search]])
            ->select('*')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'usuarios' => $user,
            'message' => 'Request successfully'
        ]);
    }

    public function saveUsuario(Request $request)
    {
        if ($request->userDetail['id'] != '') {
            $user = User::find($request->userDetail['id']);
            $valUser1 = User::where('username',  $request->userDetail['username'])->count();

            if ($valUser1 > 1) {
                $mensj1 = "EXISTE " . $valUser1 . " USUARIO(s)";
                return response()->json([
                    'success' => false,
                    'message' => 'Request error',
                    'error' => $mensj1
                ]);
            } else {
                $user->fullname = $request->userDetail['nombres'] . ' ' . $request->userDetail['apellidos'];
                $user->nombres = $request->userDetail['nombres'];
                $user->apellidos = $request->userDetail['apellidos'];
                $user->username = $request->userDetail['username'];
                if ($request->userDetail['password'] != '') {
                    $user->password = bcrypt($request->userDetail['password']);
                }
                $user->email = $request->userDetail['email'];
                
                $user->estado = 0;
               
                $user->save();
                if ($request->userDetail['id'] === '') {
                    $user->createToken('authToken')->accessToken;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Request successfully',
                ]);
            }
        } else {
            $user = new User;
            $valUser = User::where('username',  $request->userDetail['username'])->count();

            if ($valUser >= 1) {
                $mensj = "EXISTE " . $valUser . " USUARIO(s)";
                return response()->json([
                    'success' => false,
                    'message' => 'Request error',
                    'error' => $mensj
                ]);
            } else {
                $user->fullname = $request->userDetail['nombres'] . ' ' . $request->userDetail['apellidos'];
                $user->nombres = $request->userDetail['nombres'];
                $user->apellidos = $request->userDetail['apellidos'];
                $user->username = $request->userDetail['username'];
                if ($request->userDetail['password'] != '') {
                    $user->password = bcrypt($request->userDetail['password']);
                }
                $user->email = $request->userDetail['email'];
                
                $user->estado = 0;
                
                $user->save();
                if ($request->userDetail['id'] === '') {
                    $user->createToken('authToken')->accessToken;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Request successfully',
                ]);
            }
        }
    }
}
