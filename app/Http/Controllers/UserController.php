<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $limitUsers = $request->input('limit', 10);
        try {
            $users = User::limit($limitUsers)->get();
            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'users' => $users,
                'total_users' => $users->count()
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'user' => $user
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $customMessages = require resource_path('lang/es/custom_messages.php');

        $rules = [
            'nombre' => 'required',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'dni' => 'required|min:8|max:8|unique:usuarios,dni',
            'telefono' => 'required|min:9|max:9'
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors  = collect($validator->errors());
            return response()->json(['errors' => $errors], 422);
        }

        try {
            Log::info($request);
            $usuario = new User();
            $usuario->nombre = $request->input('nombre');
            $usuario->correo = $request->input('correo');
            $usuario->password = Hash::make($request->input('password'));
            $usuario->direccion = $request->input('direccion');
            $usuario->dni = $request->input('dni');
            $usuario->telefono = $request->input('telefono');
            // Obtiene el rol que se intenta asignar
            $rolId = $request->input('rol_id');
            if ($rolId) {
                $rol = Role::find($rolId);
                if (!$rol) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Rol no encontrado'
                    ], 404);
                }
                $usuario->rol_id = $rolId;
            }
            $usuario->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Cuenta creada correctamente',
                'user' => $usuario
            ], 200);
        } catch (Exception $error) {

            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $customMessages = require resource_path('lang/es/custom_messages.php');

        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $rules = [
                'nombre' => 'required',
                'correo' => 'required|email|unique:usuarios,correo,' . $id,
                'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
                'dni' => 'required|min:8|max:8|unique:usuarios,dni,' . $id,
                'telefono' => 'required|min:9|max:9'
            ];

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $errors  = collect($validator->errors());
                return response()->json(['errors' => $errors], 422);
            }

            $usuario->nombre = $request->input('nombre');
            $usuario->correo = $request->input('correo');
            $usuario->password = Hash::make($request->input('password'));
            $usuario->direccion = $request->input('direccion');
            $usuario->dni = $request->input('dni');
            $usuario->telefono = $request->input('telefono');
            $usuario->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Datos actualizados correctamente',
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => 'true',
                'message' => 'Usuario: ' . $user->nombre . ' eliminado correctamente',
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
