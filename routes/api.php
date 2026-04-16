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

    // 3.3 ESPACE CANDIDAT
    Route::middleware('role:candidat')->group(function () {

        Route::post('profil', [ProfilController::class, 'store']); 
        Route::get('profil', [ProfilController::class, 'show']); 
        
        // Postuler à une offre et voir ses candidatures
        Route::post('offres/{offre}/candidater', [CandidatureController::class, 'store']); 
        Route::get('mes-candidatures', [CandidatureController::class, 'mesCandidatures']); 


       
        Route::post('profil', [ProfilController::class, 'store']);     // Créer
        Route::get('profil', [ProfilController::class, 'show']);       // Voir
        Route::put('profil', [ProfilController::class, 'update']);     // MODIFIER (Ajouté)
        
        
        Route::post('profil/competences', [ProfilController::class, 'addCompetence']); 
        Route::delete('profil/competences/{id}', [ProfilController::class, 'removeCompetence']);

        
        Route::post('offres/{offre}/candidater', [OffreController::class, 'postuler']);
        Route::get('mes-candidatures', [OffreController::class, 'mesCandidatures']);

        Route::post('profil', [ProfilController::class, 'store']); // [cite: 53]
        Route::get('profil', [ProfilController::class, 'show']); // [cite: 55]
        Route::post('offres/{offre}/candidater', [OffreController::class, 'postuler']); // [cite: 89]

    });

    // 3.3 ESPACE RECRUTEUR
    Route::middleware('role:recruteur')->group(function () {

        Route::post('offres', [OffreController::class, 'store']); 
        
        // Voir les candidatures reçues et changer le statut
        Route::get('offres/{offre}/candidatures', [CandidatureController::class, 'indexByOffre']); 
        Route::patch('candidatures/{candidature}/statut', [CandidatureController::class, 'updateStatut']); 


        Route::post('offres', [OffreController::class, 'store']); 
        Route::get('offres/{offre}/candidatures', [OffreController::class, 'candidaturesRecues']);
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); 

        Route::post('offres', [OffreController::class, 'store']); // [cite: 78]
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); // [cite: 92]


    });

    // 3.4 ESPACE ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AdminController::class, 'indexUsers']); 
        Route::delete('admin/users/{user}', [AdminController::class, 'destroyUser']); 
        Route::patch('admin/offres/{offre}', [AdminController::class, 'toggleOffre']); 
    });

});