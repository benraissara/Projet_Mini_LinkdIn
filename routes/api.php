<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AdminController;

// --- Routes Publiques ---
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// --- Routes Protégées (JWT) ---
Route::middleware('auth:api')->group(function () {

    // ESPACE CANDIDAT
    Route::middleware('role:candidat')->group(function () {
        Route::post('profil', [ProfilController::class, 'store']); 
        Route::get('profil', [ProfilController::class, 'show']); 
        
        Route::post('offres/{offre}/candidater', [CandidatureController::class, 'store']); 
        Route::get('mes-candidatures', [CandidatureController::class, 'mesCandidatures']); 
    });

    // ESPACE RECRUTEUR
    Route::middleware('role:recruteur')->group(function () {
        Route::post('offres', [OffreController::class, 'store']); 
        
        Route::get('offres/{offre}/candidatures', [CandidatureController::class, 'indexByOffre']); 
        Route::patch('candidatures/{candidature}/statut', [CandidatureController::class, 'updateStatut']); 
    });

    // ESPACE ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AdminController::class, 'indexUsers']); 
        Route::delete('admin/users/{user}', [AdminController::class, 'destroyUser']); 
        Route::patch('admin/offres/{offre}', [AdminController::class, 'toggleOffre']); 
    });

});