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
        $seach = $request->input('search', '');
        try {
            $query = User::query();

            if($seach){
                $query->where('nombre', 'LIKE', '%' . $seach . '%');
            }
            
            // Solo trae los usuarios activos
            $query->where('activo', true);
            
            $users = $query->limit($limitUsers)->get();

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
            'correo' => 'required|email|unique:users,correo|unique:doctors,correo|unique:administrators,correo',
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'dni' => 'required|min:8|max:8|unique:users,dni|unique:doctors,dni|unique:administrators,dni',
            'telefono' => 'required|min:9|max:9'
        ];

        $errors = ValidationHelper::validate($request, $rules);
        
        if($errors){
            return $errors;
        }
        try {
            
            $user = User::create([
                'id' => (string) Str::uuid(),
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'direccion' => $request->direccion,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
            ]);

            $user->save();

            return response()->json([
                'status' => 'true',
                'message' => $user->nombre . ' tu cuenta fue creada correctamente',
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
                'correo' => 'required|email|unique:users,correo,' . $id . '|unique:doctors,correo|unique:administrators,correo',
                'dni' => 'required|min:8|max:8|unique:users,dni,' . $id . '|unique:doctors,dni|unique:administrators,dni',
                'telefono' => 'required|min:9|max:9'
            ];

            $errors = ValidationHelper::validate($request, $rules);
        
            if($errors){
                return $errors;
            }

            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->correo = $request->correo;
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

            $user->activo = false;
            $user->save();

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
