<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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

        if (!$token || !Auth::guard('sanctum')->user()) {
            return response()->json(['message' => 'Token inválido o vencido'], 401);
        }

        $request->merge(['user' => Auth::guard('sanctum')->user()]);

        return $next($request);
    }
}
