<?php

namespace App\Helpers;

class TokenHelper
{
    public static function generateToken($user)
    {
        $tokenName = env('TOKEN_NAME');      
        $token = $user->createToken($tokenName)->plainTextToken;

        return $token;
    }
}