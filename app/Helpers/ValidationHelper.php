<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidationHelper
{
    private static $customMessages;

    private static function initialize()
    {
        if (self::$customMessages === null) {
            self::$customMessages = require resource_path('lang/es/custom_messages.php');
        }
    }
    public static function validate($request, $rules)
    
    {   
        self::initialize();

        

        $validator = Validator::make($request->all(), $rules, self::$customMessages);
       
        if ($validator->fails()) {
            $errors = collect($validator->errors());
            return response()->json(['errors' => $errors], 422);
        }
        
    }
}
        