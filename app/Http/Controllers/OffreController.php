<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OffreController extends Controller
{
    
    public function postuler(Request $request, $id)
    {
        $offre = Offre::where('id', $id)->where('actif', true)->first();

        if (!$offre) {
            return response()->json(['message' => 'Offre non disponible'], 404);
        }

        $dejaPostule = Candidature::where('user_id', Auth::id())
                                   ->where('offre_id', $id)
                                   ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé'], 400);
        }

        $candidature = Candidature::create([
            'user_id' => Auth::id(),
            'offre_id' => $id,
            'statut' => 'en attente',
        ]);

        return response()->json(['message' => 'Candidature envoyée', 'data' => $candidature], 201);
    }


    public function mesCandidatures()
    {
        $candidatures = Candidature::with('offre')
                                    ->where('user_id', Auth::id())
                                    ->get();

        return response()->json($candidatures);
    }

    
    public function candidaturesRecues($id)
    {
        $offre = Offre::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée ou accès refusé'], 403);
        }

        $candidatures = Candidature::with('user')->where('offre_id', $id)->get();
        return response()->json($candidatures);
    }

    
    public function updateStatut(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:acceptée,refusée,en attente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $candidature = Candidature::findOrFail($id);
        
        
        $offre = Offre::where('id', $candidature->offre_id)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$offre) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $candidature->update(['statut' => $request->statut]);

        return response()->json(['message' => 'Statut mis à jour', 'candidature' => $candidature]);
    }
}