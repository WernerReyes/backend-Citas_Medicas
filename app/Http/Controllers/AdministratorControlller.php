<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Helpers\ValidationHelper;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class AdministratorControlller extends Controller
{
    public function index(Request $request)
    {
        $limitAdministrators = $request->input('limit', 10);
        try {

            $query = Administrator::query();

            // Veificamos si esta activo 
            $query->where('activo', $request->activo);

            $administrators = $query->limit($limitAdministrators)->get();
            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'administrators' => $administrators,
                'total_administrators' => $administrators->count()
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
            $administrator = Administrator::find($id);

            if (!$administrator) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Administrador no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'administrator' => $administrator
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

        if ($errors) {
            return $errors;
        }
        try {

            $administrator = Administrator::create([
                'id' => (string) Str::uuid(),
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'direccion' => $request->direccion,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
            ]);

            $administrator->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Cuenta creada correctamente',
                'administrator' => $administrator
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

            $administrator = Administrator::find($id);

            Log::info($administrator . ' id' . $id);

            if (!$administrator) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Administrador no encontrado'
                ], 404);
            }

            $rules = [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required|email|unique:administrators,correo,' . $id . '|unique:doctors,correo|unique:users,correo',
                // 'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
                'dni' => 'required|min:8|max:8|unique:administrators,dni,' . $id . '|unique:doctors,dni|unique:users,dni',
                'telefono' => 'required|min:9|max:9'
            ];

            $errors = ValidationHelper::validate($request, $rules);

            if ($errors) {
                return $errors;
            }

            $administrator->nombre = $request->nombre;
            $administrator->apellido = $request->apellido;
            $administrator->correo = $request->correo;
            // $administrator->password = Hash::make($request->password);
            $administrator->direccion = $request->direccion;
            $administrator->dni = $request->dni;
            $administrator->telefono = $request->telefono;
            $saved = $administrator->save();

            if (!$saved) {
                Log::info('No se pudo actualizar el administrador');
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Datos actualizados correctamente',
                'administrator' => $administrator
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
            $administrator = Administrator::find($id);

            if (!$administrator) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Administrador no encontrado'
                ], 404);
            }

            $administrator->activo = false;
            $administrator->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Administrador: ' . $administrator->nombre . ' eliminado correctamente',
                'administrator' => $administrator
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
