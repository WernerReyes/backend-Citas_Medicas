<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Doctor;
use Exception;

class DoctorsController extends Controller
{
    public function index(Request $request)
    {
        $limitDoctors = $request->input('limit', 10);
        try {
            $doctors = Doctor::limit($limitDoctors)->get();
            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'doctors' => $doctors,
                'total_doctors' => $doctors->count()
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
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'doctor' => $doctor
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     $rules = [
    //         'nombre' => 'required',
    //         'apellido' => 'required',
    //         'correo' => 'required|email|unique:users,correo',
    //         'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
    //         'dni' => 'required|min:8|max:8|unique:users,dni',
    //         'telefono' => 'required|min:9|max:9'
    //     ];

    //     $errors = ValidationHelper::validate($request, $rules);
        
    //     if($errors){
    //         return $errors;
    //     }
    //     try {
    //         $rolId = $request->rol_id;

    //         if ($rolId) {
    //             $rol = Role::find($rolId);
    //             if (!$rol) {
    //                 return response()->json([
    //                     'status' => 'false',
    //                     'message' => 'Rol no encontrado'
    //                 ], 404);
    //             }
    //         }

    //         $user = User::create([
    //             'id' => (string) Str::uuid(),
    //             'nombre' => $request->nombre,
    //             'apellido' => $request->apellido,
    //             'correo' => $request->correo,
    //             'password' => Hash::make($request->password),
    //             'direccion' => $request->direccion,
    //             'dni' => $request->dni,
    //             'telefono' => $request->telefono,
    //             'rol_id' => $rolId ?? 2
    //         ]);

    //         $user->save();

    //         return response()->json([
    //             'status' => 'true',
    //             'message' => 'Cuenta creada correctamente',
    //             'user' => $user
    //         ], 200);
    //     } catch (Exception $error) {

    //         return response()->json([
    //             'status' => 'false',
    //             'message' => $error->getMessage()
    //         ], 500);
    //     }
    // }
}
