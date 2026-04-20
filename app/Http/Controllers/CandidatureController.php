<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Events\CandidatureDeposee;
use App\Events\StatutCandidatureMis;

class CandidatureController extends Controller
{
    // --- PARTIE CANDIDAT ---
    public function store(Request $request, Offre $offre)
    {
        $user = auth()->user();
        $profil = $user->profil; 

        if (!$profil) {
            return response()->json(['message' => 'Créez d\'abord un profil'], 422);
        }

        
        $dejaPostule = Candidature::where('profil_id', $profil->id)
                                  ->where('offre_id', $offre->id)
                                  ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $candidature = Candidature::create([
            'profil_id' => $profil->id,
            'offre_id' => $offre->id,
            'message' => $request->message, 
            'statut' => 'en_attente' 
        ]);

        
        event(new CandidatureDeposee($candidature));

        return response()->json(['message' => 'Candidature envoyée !', 'candidature' => $candidature], 201);
    }

    
    public function mesCandidatures()
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé'], 404);
        }

        
        $candidatures = Candidature::where('profil_id', $profil->id)
                                   ->with('offre')
                                   ->get();

        return response()->json($candidatures, 200);
    }


    // --- PARTIE RECRUTEUR ---
    public function indexByOffre(Offre $offre)
    {
        // Règle d'ownership : 403 si l'offre n'est pas au recruteur
        if (auth()->user()->role !== 'recruteur' && $offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        
        $candidatures = $offre->candidatures()->with('profil.user')->get();
        return response()->json($candidatures, 200);
    }

    
    public function updateStatut(Request $request, Candidature $candidature)
    {
        $offre = $candidature->offre;

        // Règle d'ownership
        if (auth()->user()->role !== 'recruteur' && $offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee'
        ]);

        
        $ancienStatut = $candidature->statut;
        $nouveauStatut = $validated['statut'];

        $candidature->update(['statut' => $nouveauStatut]);

        
        if ($ancienStatut !== $nouveauStatut) {
            event(new StatutCandidatureMis($candidature, $ancienStatut, $nouveauStatut));
        }

        return response()->json(['message' => 'Statut mis à jour.', 'candidature' => $candidature], 200);
    }
}