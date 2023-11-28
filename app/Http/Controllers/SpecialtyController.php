<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

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
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 400);
            }

            $specialty = Specialty::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
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

    public function update(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 400);
            }

            $specialty = Specialty::find($request->id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $specialty->nombre = $request->nombre;
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

    public function destroy($id) {
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

