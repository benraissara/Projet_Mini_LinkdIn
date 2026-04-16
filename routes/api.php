<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;

/* --- Routes Publiques --- */
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Tout le monde (même non connecté) doit pouvoir voir les offres
Route::get('offres', [OffreController::class, 'index']);
Route::get('offres/{id}', [OffreController::class, 'show']);

/* --- Routes Protégées (JWT) --- */
Route::middleware('auth:api')->group(function () {

    // --- Espace Candidat ---
    Route::middleware('role:candidat')->group(function () {
        // Profil
        Route::post('profil', [ProfilController::class, 'store']);
        Route::get('profil', [ProfilController::class, 'show']);
        Route::put('profil', [ProfilController::class, 'update']);
        
        // Compétences
        Route::post('profil/competences', [ProfilController::class, 'addCompetence']);
        Route::delete('profil/competences/{id}', [ProfilController::class, 'removeCompetence']);

        // Candidatures (Partie 3.3)
        Route::post('offres/{id}/candidater', [OffreController::class, 'postuler']);
        Route::get('mes-candidatures', [OffreController::class, 'mesCandidatures']);
    });

    // --- Espace Recruteur ---
    Route::middleware('role:recruteur')->group(function () {
        // Gestion des Offres
        Route::post('offres', [OffreController::class, 'store']);
        Route::put('offres/{id}', [OffreController::class, 'update']); // Ownership check in Controller
        Route::delete('offres/{id}', [OffreController::class, 'destroy']); // Ownership check in Controller

        // Gestion des Candidatures Reçues (Partie 3.3)
        Route::get('offres/{id}/candidatures', [OffreController::class, 'candidaturesRecues']);
        Route::patch('candidatures/{id}/statut', [OffreController::class, 'updateStatut']);
    });

    // --- Espace Admin ---
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AuthController::class, 'index']);
        Route::delete('admin/users/{user}', [AuthController::class, 'destroy']);
    });
});