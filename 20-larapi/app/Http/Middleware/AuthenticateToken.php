<?php
// app/Http/Middleware/AuthenticateToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => '❌ Token not provided'
            ], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '❌ Invalid token'
            ], 401);
        }

        // Adjuntar usuario a la solicitud
        $request->user = $user;
        $request->user_id = $user->id;

        return $next($request);
    }
}