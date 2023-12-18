<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\ValidationHelper;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Support\Facades\Log;

class SpecialtyController extends Controller
{
    public function index()
    {
        try {
            $query = Specialty::query();
            
            // Solo trae las especialidades activas
            $query->where('activo', true);

            $specialties = $query->get();

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
    $file = $request->file('file');

    if (!$file) {
        return response()->json([
            'status' => 'false',
            'message' => 'No se envió ninguna archivo'
        ], 400);
    }

    try {
        $response = ValidationHelper::validate($request, [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($response) {
            return $response;
        }

        $nombre = strtoupper(trim($request->nombre));
        $descripcion = $request->descripcion;
    
        // Buscamos una especialidad inactiva con el mismo nombre
        $specialty = Specialty::where('nombre', $nombre)->where('activo', false)->first();

        if ($specialty) {
            // Si existe, la actualizamos y reactivamos
            $specialty->descripcion = $descripcion;
            $specialty->img = UploadHelper::upload('uploads/specialties/' . $specialty->id . '/images', $file);
            $specialty->activo = true;
        } else {
            // Si no existe, creamos una nueva
            $id = (string) Str::uuid();
            $specialty = Specialty::create([
                'id' => $id,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'img' => UploadHelper::upload('uploads/specialties/' . $id . '/images', $file)
            ]);
        }

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

    public function update(Request $request, $id)
    
    {   
        $file = $request->file('file');

        // Log::info($file . ' ' . $request->nombre . ' ' . $request->descripcion);

        // if (!$file) {
        //     return response()->json([
        //         'status' => 'false',
        //         'message' => 'No se envió ninguna archivo'
        //     ], 400);
        // }

        try {

            $specialty = Specialty::find($id);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $response = ValidationHelper::validate($request, [
                'nombre' => 'required|string|max:255|unique:specialties,nombre,' . $id,
                'descripcion' => 'required|string|max:255',
                // 'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($response) {
                return $response;
            }

            if ($specialty->img) {
                $public_id = pathinfo($specialty->img, PATHINFO_FILENAME);
                Log::info($public_id);

                // Borramos la imagen existente
                $response = Cloudinary::destroy('uploads/specialties/' . $specialty->id . '/images/' . $public_id);
            }

            $specialty->nombre = strtoupper(trim($request->nombre));
            $specialty->descripcion = $request->descripcion;
            if($file) {
            $specialty->img =  UploadHelper::upload('uploads/specialties/' . $specialty->id . '/images', $file);
            }
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

            Log::info($specialty);

            if (!$specialty) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Especialidad no encontrada'
                ], 404);
            }

            $specialty->activo = false;
            $specialty->save();

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
