<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:users,correo',
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'dni' => 'required|min:8|max:8|unique:users,dni',
            'telefono' => 'required|min:9|max:9'
        ];

        $errors = ValidationHelper::validate($request, $rules);
        
        if($errors){
            return $errors;
        }
        try {
            $rolId = $request->rol_id;

            if ($rolId) {
                $rol = Role::find($rolId);
                if (!$rol) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Rol no encontrado'
                    ], 404);
                }
            }

            $user = User::create([
                'id' => (string) Str::uuid(),
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'direccion' => $request->direccion,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
                'rol_id' => $rolId ?? 2
            ]);

            $user->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Cuenta creada correctamente',
                'user' => $user
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

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $rules = [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required|email|unique:users,correo,' . $id,
                'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
                'dni' => 'required|min:8|max:8|unique:users,dni,' . $id,
                'telefono' => 'required|min:9|max:9'
            ];

            $errors = ValidationHelper::validate($request, $rules);
        
            if($errors){
                return $errors;
            }

            $user->nombre = $request->nombre;
            $user->correo = $request->correo;
            $user->password = Hash::make($request->password);
            $user->direccion = $request->direccion;
            $user->dni = $request->dni;
            $user->telefono = $request->telefono;
            $user->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Datos actualizados correctamente',
                'user' => $user
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
                'user' => $user
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
