<?php

namespace App\Http\Controllers;

use App\Helpers\TokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('correo', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            // Accedemos al rol de usuarios
            $userRole = Role::find($user->rol_id);

            if ($userRole && $userRole->nombre === 'USER_ROLE') {
                $token = TokenHelper::generateToken($request->user(), 240);
                return response()->json([
                    'status' => 'true',
                    'token' => $token
                ], 200);
            } else {
                Auth::logout();
                return response()->json([
                    'status' => 'false',
                    'message' => 'No tienes permiso para acceder'
                ], 401);
            };
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Credenciales inválidas'
        ], 401);
    }

    public function verificarToken(Request $request)
    {
        $user = $request->user;
        return response()->json([
            'status' => 'true',
            'user' => $user
        ], 200);
    }

    public function renovarToken(Request $request)
    {
        $user = $request->user;
        $token = TokenHelper::generateToken($user, 240);
        return response()->json([
            'status' => 'true',
            'token' => $token,
            'user' => $user
        ], 200);
    }
}
