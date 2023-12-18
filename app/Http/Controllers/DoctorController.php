<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\ValidationHelper;
use App\Models\Doctor;
use App\Models\Specialty;
use Exception;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $limitDoctors = $request->input('limit', 10);
        $idSpecialty = $request->input('especialidad_id', null);
        $seach = $request->input('search', '');
        try {
            $query = Doctor::query();

            if ($idSpecialty) {
                $query->where('especialidad_id', $idSpecialty);
            }

            if($seach){
                $query->where('nombre', 'LIKE', '%' . $seach . '%');
            }

            // Solo trae los doctores activos
            $query->where('activo', true);

            // Solo trae los doctores cuya especialidad tambien este activa
            $query->whereHas('specialy', function ($query) {
                $query->where('activo', true);
            });


            $doctors = $query->limit($limitDoctors)->get();

            $doctors->load('specialy');

            // Ocultamos los campos que no queremos mostrar
            unset($doctors->especialidad_id);

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

            $doctor->load('specialy');

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

    public function store(Request $request)
    {
        // Obtenemos la imagen en caso la suba [Opcional]
        $file = $request->file('file');

        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:doctors,correo|unique:users,correo|unique:administrators,correo',
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'],
            'dni' => 'required|min:8|max:8|unique:doctors,dni|unique:users,dni|unique:administrators,dni',
            'telefono' => 'required|min:9|max:9',
            'especialidad_id' => 'required'
        ];

        $errors = ValidationHelper::validate($request, $rules);

        if ($errors) {
            return $errors;
        }
        try {

            $specialty = Specialty::find($request->especialidad_id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $id = (string) Str::uuid();

            $doctor = Doctor::create([
                'id' => $id,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'direccion' => $request->direccion,
                'dni' => $request->dni,
                'img' => ($file) ? UploadHelper::upload('uploads/doctors/' . $id . '/images', $file) : null,
                'telefono' => $request->telefono,
                'especialidad_id' => $request->especialidad_id,
            ]);

            $doctor->save();

            $doctor->load('specialy');

            return response()->json([
                'status' => 'true',
                'message' => 'Cuenta creada correctamente',
                'doctor' => $doctor
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
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            $rules = [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required|email|unique:doctors,correo,' . $id . '|unique:users,correo|unique:administrators,correo',
                'dni' => 'required|min:8|max:8|unique:doctors,dni,' . $id . '|unique:users,dni|unique:administrators,dni',
                'telefono' => 'required|min:9|max:9',
                'especialidad_id' => 'required'
            ];

            $errors = ValidationHelper::validate($request, $rules);

            if ($errors) {
                return $errors;
            }

            $specialty = Specialty::find($request->especialidad_id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $doctor->nombre = $request->nombre;
            $doctor->apellido = $request->apellido;
            $doctor->correo = $request->correo;
            $doctor->direccion = $request->direccion;
            $doctor->dni = $request->dni;
            $doctor->telefono = $request->telefono;
            $doctor->especialidad_id = $request->especialidad_id;
            $doctor->save();

            $doctor->load('specialy');

            return response()->json([
                'status' => 'true',
                'message' => 'Doctor: ' . $doctor->nombre . ' actualizado exitosamente',
                'doctor' => $doctor
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
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            $doctor->activo = false;
            $doctor->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Doctor eliminado exitosamente',
                'doctor' => $doctor
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
