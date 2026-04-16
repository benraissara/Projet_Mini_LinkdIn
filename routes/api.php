<?php

use Illuminate\Support\Facades\Route;
      
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {


    Route::middleware('role:candidat')->group(function () {
        Route::post('profil', [ProfilController::class, 'store']); // [cite: 53]
        Route::get('profil', [ProfilController::class, 'show']); // [cite: 55]
        Route::post('offres/{offre}/candidater', [OffreController::class, 'postuler']); // [cite: 89]
    });


    Route::middleware('role:recruteur')->group(function () {
        Route::post('offres', [OffreController::class, 'store']); // [cite: 78]
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); // [cite: 92]
    });


    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AuthController::class, 'index']); // [cite: 98]
        Route::delete('admin/users/{user}', [AuthController::class, 'destroy']); // [cite: 99]
    });
});