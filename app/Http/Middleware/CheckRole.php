<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Middleware\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole extends Middleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérification de l'authentification
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        // Vérification du rôle [cite: 15, 45]
        if (!$user || !in_array($user->role, $roles)) {
            // Retourne une erreur 403 comme exigé par le projet 
            return response()->json(['error' => 'Accès interdit - 403'], 403);
        }

        return $next($request);
    }
}