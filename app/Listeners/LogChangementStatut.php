<?php

namespace App\Listeners;

use App\Events\StatutCandidatureMis;
use Illuminate\Support\Facades\Log;

class LogChangementStatut
{
    /**
     * Handle the event.
     */
    public function handle(StatutCandidatureMis $event): void
    {
        // Utilise le canal 'candidatures' créé dans config/logging.php
        Log::channel('candidatures')->info("Candidature ID: {$event->candidature->id} (Offre ID: {$event->candidature->offre_id}) | Statut passé de '{$event->ancienStatut}' à '{$event->nouveauStatut}'.");
    }
}