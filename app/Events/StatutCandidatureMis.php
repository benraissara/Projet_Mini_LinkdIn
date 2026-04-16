<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatutCandidatureMis
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candidature;
    public $ancienStatut;
    public $nouveauStatut;

    /**
     * Create a new event instance.
     */
    public function __construct(Candidature $candidature, $ancienStatut, $nouveauStatut)
    {
        $this->candidature = $candidature;
        $this->ancienStatut = $ancienStatut;
        $this->nouveauStatut = $nouveauStatut;
    }
}