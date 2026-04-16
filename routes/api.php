<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;



// --- ROUTES PUBLIQUES ---
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Consultation des offres (accessible à tous sans être connecté) 
Route::get('offres', [OffreController::class, 'index']);
Route::get('offres/{offre}', [OffreController::class, 'show']);


// --- ROUTES PROTÉGÉES PAR JWT ---
Route::middleware('auth:api')->group(function () {

    // Route pour récupérer l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- ESPACE CANDIDATS --- 
    Route::middleware('role:candidat')->group(function () {
        Route::post('profil', [ProfilController::class, 'store']); // Créer profil [cite: 52]
        Route::get('profil', [ProfilController::class, 'show']);   // Consulter profil [cite: 54]
        Route::put('profil', [ProfilController::class, 'update']); // Modifier profil [cite: 56]
        
        // Candidater à une offre 
        Route::post('offres/{offre}/candidater', [OffreController::class, 'postuler']);
        Route::get('mes-candidatures', [OffreController::class, 'mesCandidatures']); // [cite: 90]
    });

    // --- ESPACE RECRUTEURS --- 
    Route::middleware('role:recruteur')->group(function () {
        // Gestion des offres (ton CRUD) [cite: 78, 80, 81]
        Route::post('offres', [OffreController::class, 'store']);
        Route::put('offres/{offre}', [OffreController::class, 'update']);
        Route::delete('offres/{offre}', [OffreController::class, 'destroy']);
        
        // Gestion des candidatures reçues 
        Route::get('offres/{offre}/candidatures', [OffreController::class, 'candidaturesRecues']);
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']);
    });

    // --- ESPACE ADMIN --- 
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AuthController::class, 'index']); // Liste users [cite: 98]
        Route::delete('admin/users/{user}', [AuthController::class, 'destroy']); // Supprimer user [cite: 99]
        Route::patch('admin/offres/{offre}', [OffreController::class, 'toggleOffre']); // Activer/Désactiver [cite: 101]
    });

});