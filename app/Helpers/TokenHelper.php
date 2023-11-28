<?php

namespace App\Helpers;

class TokenHelper
{
    public static function generateToken($user, $expiration)
    {
        $tokenName = env('TOKEN_NAME');      
        return $user->createToken($tokenName, ['expires_in' => $expiration])->plainTextToken;
    }
}
