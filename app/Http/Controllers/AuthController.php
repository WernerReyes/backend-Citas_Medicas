<?php

namespace App\Http\Controllers;

use App\Helpers\TokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('correo', 'password');

            if (Auth::attempt($credentials)) {
                $user = $request->user();
                // Accedemos al rol de usuarios
                $userRole = Role::find($user->rol_id);

                // Si el rol es USER_ROLE, generamos un nuevo token
                if ($userRole && $userRole->nombre === 'USER_ROLE') {
                    $token = TokenHelper::generateToken($user);
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
                'status' => 'false',
                'message' => 'Credenciales inválidas'
            ], 401);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
    public function loginPersonalClinica(Request $request)
    {
        try {
            $credentials = $request->only('correo', 'password');

            if (Auth::guard('doctor')->attempt($credentials) || Auth::guard('admin')->attempt($credentials)) {
                $personal = Auth::guard('doctor')->user() ?? Auth::guard('admin')->user();
              
                // Accedemos al rol de usuarios
                $personalRole = Role::find($personal->rol_id);
    
                // Si el rol es USER_ROLE, generamos un nuevo token
                if ($personalRole && ($personalRole->nombre === 'MEDICAL_ROLE' || $personalRole->nombre === 'ADMIN_ROLE')) {
                    $token = TokenHelper::generateToken($personal);
                    return response()->json([
                        'status' => 'true',
                        'rol' => $personalRole->nombre,
                        'token' => $token
                    ], 200);
                }
    
                Auth::logout();
                return response()->json([
                    'status' => 'false',
                    'message' => 'No tienes permiso para acceder'
                ], 401);
            }
    
            return response()->json([
                'status' => 'false',
                'message' => 'No tienes permiso para acceder'
            ], 401);
        } catch(Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
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
        Log::info($user);
        $user->tokens()->delete();
        $token = TokenHelper::generateToken($user);
        return response()->json([
            'status' => 'true',
            'token' => $token,
            'user' => $user
        ], 200);
    }
}
