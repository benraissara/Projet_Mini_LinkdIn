<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompetenceProfilController;

// --- Routes Publiques ---
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('offres', [OffreController::class, 'index']); // Liste des offres
Route::get('offres/{offre}', [OffreController::class, 'show']); // Détail d'une offre

// --- Routes Protégées (JWT) ---
Route::middleware('auth:api')->group(function () {

    // ESPACE CANDIDAT
    Route::middleware('role:candidat')->group(function () {
        // Profil
        Route::post('profil', [ProfilController::class, 'store']); 
        Route::get('profil', [ProfilController::class, 'show']); 
        Route::put('profil', [ProfilController::class, 'update']); // Modification profil
        
        // Compétences
        Route::post('profil/competences', [ProfilController::class, 'addCompetence']); 
        Route::delete('profil/competences/{competence}', [CompetenceProfilController::class, 'destroy']); 
        
        // Candidatures
        Route::post('offres/{offre}/candidater', [CandidatureController::class, 'store']); 
        Route::get('mes-candidatures', [CandidatureController::class, 'mesCandidatures']); 
    });

    // ESPACE RECRUTEUR
    Route::middleware('role:recruteur')->group(function () {
        Route::post('offres', [OffreController::class, 'store']); 
        Route::put('offres/{offre}', [OffreController::class, 'update']); // Modifier offre
        Route::delete('offres/{offre}', [OffreController::class, 'destroy']); // Supprimer offre
        
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