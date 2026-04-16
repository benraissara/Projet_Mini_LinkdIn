<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth:api')->group(function () {

    
    Route::middleware('role:candidat')->group(function () {
       
        Route::post('profil', [ProfilController::class, 'store']);     // Créer
        Route::get('profil', [ProfilController::class, 'show']);       // Voir
        Route::put('profil', [ProfilController::class, 'update']);     // MODIFIER (Ajouté)
        
        
        Route::post('profil/competences', [ProfilController::class, 'addCompetence']); 
        Route::delete('profil/competences/{id}', [ProfilController::class, 'removeCompetence']);

        
        Route::post('offres/{offre}/candidater', [OffreController::class, 'postuler']);
    });

    
    Route::middleware('role:recruteur')->group(function () {
        Route::post('offres', [OffreController::class, 'store']); 
        Route::patch('candidatures/{candidature}/statut', [OffreController::class, 'updateStatut']); 
    });

    
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [AuthController::class, 'index']); 
        Route::delete('admin/users/{user}', [AuthController::class, 'destroy']); 
    });
});