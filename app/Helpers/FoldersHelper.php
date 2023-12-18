<?php

namespace App\Helpers;

use App\Models\Administrator;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Exception;

class FoldersHelper
{

    public static function modeloFolders($id, $folder)
    
    { 
        $modelo = null;
        
        switch($folder){
            case 'users':
                $modelo = User::find($id);
                if(!$modelo){
                    throw new Exception('No existe el usuario con el id ' . $id . ' en la base de datos');
                }
                break;
            case 'doctors':
                $modelo = Doctor::find($id);
                if(!$modelo){
                    throw new Exception('No existe el doctor con el id ' . $id . ' en la base de datos');
                }
                break;
                
            case 'specialties':
                $modelo = Specialty::find($id);
                if(!$modelo){
                    throw new Exception('No existe la especialidad con el id ' . $id . ' en la base de datos');
                }
                break;
            case 'administrators':
                $modelo = Administrator::find($id);
                if(!$modelo){
                    throw new Exception('No existe el administrador con el id ' . $id . ' en la base de datos');
                }
                break;
            default:
                throw new Exception('No existe el folder');
        }

        return $modelo;
       
        
    }
}
        