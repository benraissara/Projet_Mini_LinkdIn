<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Events\StatutCandidatureMis;

class CandidatureController extends Controller
{
    // --- PARTIE CANDIDAT ---

    // POST /api/offres/{offre}/candidater
    public function store(Offre $offre)
    {
        $user = auth()->user();

        // Vérifier si le candidat a déjà postulé (évite les doublons)
        $dejaPostule = Candidature::where('user_id', $user->id)
                                 ->where('offre_id', $offre->id)
                                 ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $candidature = Candidature::create([
            'user_id' => $user->id,
            'offre_id' => $offre->id,
            'statut' => 'en_attente' // Statut par défaut
        ]);

        return response()->json(['message' => 'Candidature envoyée !', 'candidature' => $candidature], 201);
    }

    // GET /api/mes-candidatures
    public function mesCandidatures()
    {
        // Règle d'ownership : Un candidat ne peut consulter que ses propres candidatures
        $candidatures = Candidature::where('user_id', auth()->id())
                                   ->with('offre')
                                   ->get();

        return response()->json($candidatures, 200);
    }


    // --- PARTIE RECRUTEUR ---

    // GET /api/offres/{offre}/candidatures
    public function indexByOffre(Offre $offre)
    {
        // Règle d'ownership : 403 si l'offre n'est pas au recruteur
        if (auth()->user()->role !== 'admin' && $offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        $candidatures = $offre->candidatures()->with('user')->get();
        return response()->json($candidatures, 200);
    }

    // PATCH /api/candidatures/{candidature}/statut
    public function updateStatut(Request $request, Candidature $candidature)
    {
        $offre = $candidature->offre;

        // Règle d'ownership
        if (auth()->user()->role !== 'admin' && $offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:en attente,acceptee,refusee'
        ]);

        // On sauvegarde l'ancien statut avant la mise à jour
        $ancienStatut = $candidature->statut;
        $nouveauStatut = $validated['statut'];

        $candidature->update(['statut' => $nouveauStatut]);

        // Si le statut a réellement changé, on déclenche l'Event
        if ($ancienStatut !== $nouveauStatut) {
            StatutCandidatureMis::dispatch($candidature, $ancienStatut, $nouveauStatut);
        }

        return response()->json(['message' => 'Statut mis à jour.', 'candidature' => $candidature], 200);
    }
}