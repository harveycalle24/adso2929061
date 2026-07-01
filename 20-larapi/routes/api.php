<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PetController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - Larapets
|--------------------------------------------------------------------------
*/

// 🟢 LOGIN - Público
// Endpoint: http://127.0.0.1:8000/api/login
Route::post('/login', [AuthController::class, 'login']);

// 🔵 LOGOUT - Protegido
// Endpoint: http://127.0.0.1:8000/api/logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.token');

// 🔵 RUTAS PROTEGIDAS DE PETS
Route::middleware('auth.token')->group(function () {
    
    // Listar todas las mascotas
    // Endpoint: http://127.0.0.1:8000/api/pets/list
    Route::get('pets/list', [PetController::class, 'index']);
    
    // Mostrar una mascota específica
    // Endpoint: http://127.0.0.1:8000/api/pets/show/{id}
    Route::get('pets/show/{id}', [PetController::class, 'show']);
    
    // Crear una nueva mascota
    // Endpoint: http://127.0.0.1:8000/api/pets/store
    Route::post('pets/store', [PetController::class, 'store']);
    
    // Actualizar una mascota
    // Endpoint: http://127.0.0.1:8000/api/pets/edit/{id}
    Route::put('pets/edit/{id}', [PetController::class, 'update']);
    
    // Eliminar una mascota
    // Endpoint: http://127.0.0.1:8000/api/pets/delete/{id}
    Route::delete('pets/delete/{id}', [PetController::class, 'destroy']);
});