<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UploadHelper
{
    public static function upload($directory, $file)
    {
        // Nombre del directorio basado en el id del modelo
     
        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => $directory,
        ]);

        // Obtenemos la url de la imagen
        $uploadedFileUrl = $uploadedFile->getSecurePath();

        return $uploadedFileUrl;
    }
}