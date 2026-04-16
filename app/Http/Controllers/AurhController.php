<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // <-- Importez la façade Auth

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // ... votre validation ...

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Précisez le guard 'api' pour que l'IDE comprenne qu'on utilise JWT
        /** @var \Tymon\JWTAuth\JWT $auth */
        $token = Auth::guard('api')->login($user); 

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // On utilise Auth::guard('api') pour appeler attempt()
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => Auth::guard('api')->user()
        ]);
    }
}