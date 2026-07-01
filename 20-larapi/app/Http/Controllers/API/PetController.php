<?php
// app/Http/Controllers/API/PetController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    /**
     * List Pets - Listar todas las mascotas
     * GET /api/pets/list
     */
    public function index()
    {
        try {
            $pets = Pet::all();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Pets retrieved successfully!',
                'pets' => $pets,
                'total' => $pets->count()
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ Error fetching pets:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Error fetching pets!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Pet - Mostrar una mascota específica
     * GET /api/pets/show/{id}
     */
    public function show($id)
    {
        try {
            $pet = Pet::find($id);
            
            if (!$pet) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Pet not found!'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => '✅ Pet retrieved successfully!',
                'pet' => $pet
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ Error fetching pet:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Error fetching pet!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store Pet - Crear una nueva mascota
     * POST /api/pets/store
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== STORE PET REQUEST ===');
            
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'kind' => ['required', 'string', 'max:255'],
                'weight' => ['required', 'numeric', 'min:0', 'max:200'],
                'age' => ['required', 'integer', 'min:0', 'max:360'],
                'breed' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'active' => ['sometimes', 'boolean'],
                'adopted' => ['sometimes', 'boolean'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,bmp', 'max:5120']
            ]);

            if ($validator->fails()) {
                Log::error('❌ Validation error:', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Validation error!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Manejar imagen
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $newImageName = time() . '.' . $extension;
                
                $file->storeAs('pets', $newImageName, 'public');
                $data['image'] = $newImageName;
                Log::info('✅ Image saved:', ['image' => $newImageName]);
            } else {
                $data['image'] = 'no-image.png';
                Log::info('⚠️ No image uploaded, using default');
            }

            $data['active'] = $data['active'] ?? 1;
            $data['adopted'] = $data['adopted'] ?? 0;

            $pet = Pet::create($data);

            Log::info('✅ Pet created:', ['pet' => $pet]);

            return response()->json([
                'success' => true,
                'message' => '✅ Pet created successfully!',
                'pet' => $pet
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ Error creating pet:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Pet - Actualizar una mascota
     * PUT /api/pets/edit/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('=== UPDATE PET REQUEST ===');
            Log::info('ID:', [$id]);
            
            $pet = Pet::find($id);
            
            if (!$pet) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Pet not found!'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'kind' => ['sometimes', 'required', 'string', 'max:255'],
                'weight' => ['sometimes', 'required', 'numeric', 'min:0', 'max:200'],
                'age' => ['sometimes', 'required', 'integer', 'min:0', 'max:360'],
                'breed' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'active' => ['sometimes', 'boolean'],
                'adopted' => ['sometimes', 'boolean'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,bmp', 'max:5120']
            ]);

            if ($validator->fails()) {
                Log::error('❌ Validation error:', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Validation error!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Manejar imagen
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Eliminar imagen anterior si existe
                if ($pet->image && $pet->image !== 'no-image.png') {
                    if (Storage::disk('public')->exists('pets/' . $pet->image)) {
                        Storage::disk('public')->delete('pets/' . $pet->image);
                        Log::info('🗑️ Old image deleted:', ['image' => $pet->image]);
                    }
                }
                
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $newImageName = time() . '.' . $extension;
                
                $file->storeAs('pets', $newImageName, 'public');
                $data['image'] = $newImageName;
                Log::info('✅ New image saved:', ['image' => $newImageName]);
            }

            $pet->update($data);

            Log::info('✅ Pet updated:', ['pet' => $pet]);

            return response()->json([
                'success' => true,
                'message' => '✅ Pet updated successfully!',
                'pet' => $pet
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error updating pet:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Pet - Eliminar una mascota
     * DELETE /api/pets/delete/{id}
     */
    public function destroy($id)
    {
        try {
            Log::info('=== DELETE PET REQUEST ===');
            Log::info('ID:', [$id]);
            
            $pet = Pet::find($id);
            
            if (!$pet) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Pet not found!'
                ], 404);
            }

            // Eliminar imagen si existe
            if ($pet->image && $pet->image !== 'no-image.png') {
                if (Storage::disk('public')->exists('pets/' . $pet->image)) {
                    Storage::disk('public')->delete('pets/' . $pet->image);
                    Log::info('🗑️ Image deleted:', ['image' => $pet->image]);
                }
            }

            $pet->delete();

            Log::info('✅ Pet deleted:', ['pet' => $pet]);

            return response()->json([
                'success' => true,
                'message' => '✅ Pet deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error deleting pet:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}