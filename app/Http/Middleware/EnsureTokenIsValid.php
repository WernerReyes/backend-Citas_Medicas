<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        Log::info(Auth::guard('sanctum')->user());

        if (!$token || !Auth::guard('sanctum')->user()) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        $request->merge(['user' => Auth::guard('sanctum')->user()]);

        return $next($request);
    }
}
