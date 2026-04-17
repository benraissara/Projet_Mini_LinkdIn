<?php

namespace App\Listeners;

use App\Events\CandidatureDeposee;
use Illuminate\Support\Facades\Log;

class LogCandidatureDeposee
{
    public function handle(CandidatureDeposee $event): void
    {
        Log::channel('candidatures')->info('Candidature déposée', [
            'date' => now()->toDateTimeString(),
            'candidat' => $event->candidature->profil->user->name ?? 'Inconnu',
            'offre' => $event->candidature->offre->titre ?? 'Inconnue',
        ]);
    }
}