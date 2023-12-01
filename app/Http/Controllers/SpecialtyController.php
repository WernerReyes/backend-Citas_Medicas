<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\ValidationHelper;
use Exception;
use Illuminate\Support\Facades\Log;

class SpecialtyController extends Controller
{
    public function index()
    {
        try {
            $specialties = Specialty::all();
            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'specialties' => $specialties,
                'total_specialties' => $specialties->count()
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
            $specialty = Specialty::find($id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'specialty' => $specialty
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
        // Obtenemos la imagen
        // $file = $request->file('file');

        // if (!$file) {
        //     return response()->json([
        //         'status' => 'false',
        //         'message' => 'No se envió ninguna archivo'
        //     ], 400);
        // }

        try {

            $response = ValidationHelper::validate($request, [
                'nombre' => 'required|string|max:255|unique:specialties,nombre',
                'descripcion' => 'required|string|max:255',
                // 'img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($response) {
                return $response;
            }
            
            $id = (string) Str::uuid();

            $specialty = Specialty::create([
                'id' => $id,
                'nombre' => strtoupper(trim($request->nombre)),
                'descripcion' => $request->descripcion,
                // 'img' => UploadHelper::upload('uploads/specialties/' . $id . '/images', $file),
            ]);
            $specialty->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Especialidad: ' . $specialty->nombre . ' creada exitosamente',
                'specialty' => $specialty
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {

            $specialty = Specialty::find($request->id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $response = ValidationHelper::validate($request, [
                'nombre' => 'required|string|max:255|unique:specialties,nombre',
                'descripcion' => 'required|string|max:255',
            ]);
            if ($response) {
                return $response;
            }

            $specialty->nombre = strtoupper(trim($request->nombre));
            $specialty->descripcion = $request->descripcion;
            $specialty->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Especialidad: ' . $specialty->nombre . ' actualizada exitosamente',
                'specialty' => $specialty
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
            $specialty = Specialty::find($id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $specialty->delete();

            return response()->json([
                'status' => 'true',
                'message' => 'Especialidad: ' . $specialty->nombre . ' eliminada exitosamente',
                'specialty' => $specialty
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
