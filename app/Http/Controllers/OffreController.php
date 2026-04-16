<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OffreController extends Controller
{
    // 1. POST /api/offres/{id}/candidater (Candidat)
    public function postuler(Request $request, $id)
    {
        $offre = Offre::where('id', $id)->where('actif', true)->first();

        if (!$offre) {
            return response()->json(['message' => 'Offre non disponible'], 404);
        }

        // Vérifier si le candidat a déjà postulé
        $dejaPostule = Candidature::where('user_id', Auth::id())
                                   ->where('offre_id', $id)
                                   ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre'], 400);
        }

        $candidature = Candidature::create([
            'user_id' => Auth::id(),
            'offre_id' => $id,
            'statut' => 'en attente',
        ]);

        return response()->json(['message' => 'Candidature envoyée', 'data' => $candidature], 201);
    }

    // 2. GET /api/mes-candidatures (Candidat)
    // Règle d'ownership : On ne récupère que celles liées à Auth::id()
    public function mesCandidatures()
    {
        $candidatures = Candidature::with('offre')
                                    ->where('user_id', Auth::id())
                                    ->get();

        return response()->json($candidatures);
    }

    // 3. GET /api/offres/{id}/candidatures (Recruteur propriétaire)
    public function candidaturesRecues($id)
    {
        $offre = Offre::findOrFail($id);

        // Règle d'ownership : Seul le propriétaire de l'offre voit les candidats
        if ($offre->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès interdit - 403'], 403);
        }

        $candidatures = Candidature::with('user')->where('offre_id', $id)->get();
        return response()->json($candidatures);
    }

    // 4. PATCH /api/candidatures/{id}/statut (Recruteur propriétaire)
    public function updateStatut(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:acceptée,refusée,en attente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $candidature = Candidature::findOrFail($id);
        $offre = Offre::findOrFail($candidature->offre_id);

        // Règle d'ownership : Seul le créateur de l'offre peut changer le statut
        if ($offre->user_id !== Auth::id()) {
            return response()->json(['message' => 'Action non autorisée - 403'], 403);
        }

        $candidature->update(['statut' => $request->statut]);

        return response()->json(['message' => 'Statut mis à jour', 'data' => $candidature]);
    }
}