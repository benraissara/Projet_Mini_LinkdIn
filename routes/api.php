<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;

// Routes Publiques
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Routes Protégées par JWT
Route::middleware('auth:api')->group(function () {

    // --- Espaces Candidats ---
    Route::middleware('role:candidat')->group(function () {

       
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

    // --- Espaces Recruteurs ---
    Route::middleware('role:recruteur')->group(function () {

        Route::post('offres', [OffreController::class, 'store']); 
        Route::get('offres/{offre}/candidatures', [OffreController::class, 'candidaturesRecues']);
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); 

        Route::post('offres', [OffreController::class, 'store']); // [cite: 78]
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); // [cite: 92]

    });

    // --- Espace Admin ---
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AuthController::class, 'index']); // [cite: 98]
        Route::delete('admin/users/{user}', [AuthController::class, 'destroy']); // [cite: 99]
    });

});