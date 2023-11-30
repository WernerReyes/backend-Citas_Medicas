<?php

namespace App\Http\Controllers;

use App\Helpers\FoldersHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\UploadHelper;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function store(Request $request, $folder, $model, $id)
    {
        // Obtenemos la imagen
        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'status' => 'false',
                'message' => 'No se envió ninguna archivo'
            ], 400);
        }

        $rules = [
            'image' => ['file' => 'required|image|mimes:jpeg,png,jpg|max:2048'],
            'document' => ['file' => 'required|mimes:pdf|max:2048']
        ];

        $validationRules = ($folder === 'images') ? $rules['image'] : $rules['document'];
        $response = ValidationHelper::validate($request, $validationRules);

        if ($response) {
            return $response;
        }

        try {
            // Obtenemos el modelo [user, specialty] que deseamos cargar el folder
            $modelo = FoldersHelper::modeloFolders($id, $model);

            // Nombre del directorio basado en el id del modelo
            $directory = 'uploads/' . $model . '/' . $modelo->id . '/' . $folder;

            // $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            //     'folder' => $directory,
            // ]);

            // // Obtenemos la url de la imagen
            // $uploadedFileUrl = $uploadedFile->getSecurePath();

            // Guardamos la url de la imagen en la base de datos
            $modelo->img = UploadHelper::upload($directory, $file);
            $modelo->save();

            return response()->json([
                'status' => 'true',
                'message' => ($folder === 'images' ? 'Imagen subida' : 'Documento subido') . ' exitosamente',
                'user' => $modelo
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $folder, $model, $id)
    {
        // Obtenemos la imagen
        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'status' => 'false',
                'message' => 'No se envió ninguna archivo'
            ], 400);
        }

        $rules = [
            'image' => ['file' => 'required|image|mimes:jpeg,png,jpg|max:2048'],
            'document' => ['file' => 'required|mimes:pdf|max:2048']
        ];

        $validationRules = ($folder === 'images') ? $rules['image'] : $rules['document'];

        $response = ValidationHelper::validate($request, $validationRules);

        if ($response) {
            return $response;
        }

        try {
            // Obtenemos el modelo [user, specialty] que deseamos cargar el folder
            $modelo = FoldersHelper::modeloFolders($id, $model);

            // Nombre del directorio basado en el id del modelo
            $directory = 'uploads/' . $model . '/' . $modelo->id . '/' . $folder;

            if ($modelo->img) {
                $public_id = pathinfo($modelo->img, PATHINFO_FILENAME);
                Log::info($public_id);

                // Borramos la imagen existente
                $response = Cloudinary::destroy($directory . '/' . $public_id);
            }

            // $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            //     'folder' => $directory,
            // ]);

            // // Obtenemos la url de la imagen
            // $uploadedFileUrl = $uploadedFile->getSecurePath();

            // Guardamos la url de la imagen en la base de datos
            $modelo->img = UploadHelper::upload($directory, $file);
            $modelo->save();

            return response()->json([
                'status' => 'true',
                'message' => ($folder === 'images' ? 'Imagen ' : 'Documento ') . 'editado exitosamente',
                'user' => $modelo
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

}
