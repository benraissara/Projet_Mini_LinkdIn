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

    // POST /api/offres/{offre}/candidater
    public function store(Request $request, Offre $offre)
    {
        $user = auth()->user();
        $profil = $user->profil; // On récupère le profil du candidat, pas l'user

        if (!$profil) {
            return response()->json(['message' => 'Créez d\'abord un profil'], 422);
        }

        // Vérifier si le candidat a déjà postulé (évite les doublons)
        $dejaPostule = Candidature::where('profil_id', $profil->id)
                                  ->where('offre_id', $offre->id)
                                  ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $candidature = Candidature::create([
            'profil_id' => $profil->id,
            'offre_id' => $offre->id,
            'message' => $request->message, // Prise en compte du message
            'statut' => 'en_attente' // Statut par défaut
        ]);

        // 🔥 DÉCLENCHER L'EVENT DE DÉPÔT
        event(new CandidatureDeposee($candidature));

        return response()->json(['message' => 'Candidature envoyée !', 'candidature' => $candidature], 201);
    }

    // GET /api/mes-candidatures
    public function mesCandidatures()
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé'], 404);
        }

        // Règle d'ownership : Un candidat ne peut consulter que ses propres candidatures
        $candidatures = Candidature::where('profil_id', $profil->id)
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

        // On charge les candidatures avec le profil et l'utilisateur lié
        $candidatures = $offre->candidatures()->with('profil.user')->get();
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

        // CORRECTION ICI : "en_attente" avec un underscore (underscore)
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee'
        ]);

        // On sauvegarde l'ancien statut avant la mise à jour
        $ancienStatut = $candidature->statut;
        $nouveauStatut = $validated['statut'];

        $candidature->update(['statut' => $nouveauStatut]);

        // Si le statut a réellement changé, on déclenche l'Event de modification
        if ($ancienStatut !== $nouveauStatut) {
            event(new StatutCandidatureMis($candidature, $ancienStatut, $nouveauStatut));
        }

        return response()->json(['message' => 'Statut mis à jour.', 'candidature' => $candidature], 200);
    }
}