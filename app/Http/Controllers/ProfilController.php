<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'localisation' => 'required|string',
            'disponible' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        
        $profil = Profil::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'bio' => $request->bio,
            'localisation' => $request->localisation,
            'disponible' => $request->disponible ?? true,
        ]);

        return response()->json(['message' => 'Profil créé', 'profil' => $profil], 201);
    }

    public function show()
    {
        $profil = Profil::with('competences')->where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Profil non trouvé'], 404);
        }

        return response()->json($profil);
    }


    public function update(Request $request)
    {
        $profil = Profil::where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Profil inexistant'], 404);
        }

        $profil->update($request->only(['titre', 'bio', 'localisation', 'disponible']));

        return response()->json(['message' => 'Profil mis à jour', 'profil' => $profil]);
    }

    
    public function addCompetence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'competence_id' => 'required|exists:competences,id',
            'niveau' => 'required|in:débutant,intermédiaire,expert',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $profil = Auth::user()->profil;
        
        
        $profil->competences()->attach($request->competence_id, ['niveau' => $request->niveau]);

        return response()->json(['message' => 'Compétence ajoutée']);
    }
}