<?php
// app/Http/Controllers/API/UserController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Query success!',
                'users' => $users,
                'total' => $users->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error fetching users!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ User not found!'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => '✅ Query success!',
                'user' => $user
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error fetching user!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            Log::info('=== USER STORE REQUEST ===');

            $validator = Validator::make($request->all(), [
                'document' => ['required', 'numeric', 'unique:users'],
                'fullname' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'in:male,female,Male,Female'],
                'birthdate' => ['required', 'date'],
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Validation error!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            $data['photo'] = 'images/users/no-photo.png';
            $data['active'] = 1;
            $data['role'] = 'customer';
            
            $user = User::create($data);
            
            Log::info('✅ User created:', [$user->email]);
            
            return response()->json([
                'success' => true,
                'message' => '✅ User created successfully!',
                'user' => $user
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('❌ Error creating user:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Error creating user!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('=== USER UPDATE REQUEST ===');
            Log::info('ID:', [$id]);
            
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ User not found!'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'document' => ['sometimes', 'numeric', 'unique:users,document,' . $id],
                'fullname' => ['sometimes', 'string', 'max:255'],
                'gender' => ['sometimes', 'in:male,female,Male,Female'],
                'birthdate' => ['sometimes', 'date'],
                'phone' => ['sometimes', 'string', 'max:20'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'password' => ['sometimes', 'string', 'min:8']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Validation error!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $user->update($data);
            
            Log::info('✅ User updated:', [$user->email]);
            
            return response()->json([
                'success' => true,
                'message' => '✅ User updated successfully!',
                'user' => $user
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ Error updating user:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Error updating user!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            Log::info('=== USER DELETE REQUEST ===');
            Log::info('ID:', [$id]);
            
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ User not found!'
                ], 404);
            }
            
            $user->delete();
            
            Log::info('✅ User deleted:', [$user->email]);
            
            return response()->json([
                'success' => true,
                'message' => '✅ User deleted successfully!'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ Error deleting user:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Error deleting user!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}