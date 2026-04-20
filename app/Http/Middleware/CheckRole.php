<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole 
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        
        if (!$user || !in_array($user->role, $roles)) {
       
            return response()->json(['error' => 'Accès interdit - 403'], 403);
        }

        return $next($request);
    }
}