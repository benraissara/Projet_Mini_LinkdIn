<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OffreController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// 1. ROUTES PUBLIQUES (accessibles sans être connecté)
Route::apiResource('offres', OffreController::class)->only(['index', 'show']);

// 2. ROUTES PROTÉGÉES (il faut un token Sanctum pour y accéder)
Route::middleware('auth:api')->group(function () {
    Route::apiResource('offres', OffreController::class)->except(['index', 'show']);
});