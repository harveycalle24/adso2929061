<?php
// app/Http/Controllers/API/AuthController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Login - Iniciar sesión
     * POST /api/login
     */
    public function login(Request $request)
    {
        try {
            Log::info('=== LOGIN REQUEST ===');
            Log::info('Email:', [$request->email]);

            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('❌ Invalid credentials for:', [$request->email]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Invalid credentials!'
                ], 401);
            }

            $token = Str::random(60);
            $user->update(['remember_token' => $token]);
            
            Log::info('✅ Login successful for:', [$user->email]);

            return response()->json([
                'success' => true,
                'message' => '✅ Login successful!',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'document' => $user->document,
                    'role' => $user->role,
                    'photo' => $user->photo ?? 'images/users/no-photo.png'
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Validation error!',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Login error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout - Cerrar sesión
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        try {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            $user = User::where('remember_token', $token)->first();
            
            if ($user) {
                $user->update(['remember_token' => null]);
                Log::info('✅ Logout successful for:', [$user->email]);
                return response()->json([
                    'success' => true,
                    'message' => '✅ Logout successful!'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => '❌ Invalid token!'
            ], 401);

        } catch (\Exception $e) {
            Log::error('❌ Logout error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}