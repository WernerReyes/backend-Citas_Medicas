<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class UploadController extends Controller
{
    public function store(Request $request, $folder)
    {

        // Identificamos al usuario
        $user = $request->user;

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

            // Nombre del disrectorio basado en el id del usuario
            $directory = 'uploads/' . $user->id . '/' . $folder;

            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => $directory,
            ]);

            // Obtenemos la url de la imagen
            $uploadedFileUrl = $uploadedFile->getSecurePath();

            // Guardamos la url de la imagen en la base de datos
            $user->foto_perfil = $uploadedFileUrl;
            $user->save();

            return response()->json([
                'status' => 'true',
                'message' => ($folder === 'images' ? 'Imagen subida' : 'Documento subido') . ' exitosamente',
                'user' => $user
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $folder, $id)
    {
        try {

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtenemos la imagen
            $file = $request->file('file');

            if (!$file) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se envió ninguna imagen'
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

            if ($user->foto_perfil) {
                $nombreArr = explode('/', $user->foto_perfil);
                $nombre  = $nombreArr[count($nombreArr) - 1];
                list($public_id) = explode('.', $nombre);

                // Borramos la imagen existente
                Cloudinary::destroy($public_id);
            }

            $directory = 'uploads/' . $user->id . '/' . $folder;

            // Subimos la nueva imagen con el mismo public_id
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => $directory,
                'public_id' => $public_id
            ]);

            // Obtenemos la url de la imagen
            $uploadedFileUrl = $uploadedFile->getSecurePath();

            // Guardamos la url de la imagen en la base de datos
            $user->foto_perfil = $uploadedFileUrl;
            $user->save();

            return response()->json([
                'status' => 'true',
                'message' => ($folder === 'images' ? 'Imagen ' : 'Documento ') . 'actualizado exitosamente',
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
