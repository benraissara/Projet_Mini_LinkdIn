<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatutCandidatureMis
{
    use Dispatchable, SerializesModels;

    public $candidature;
    public $ancienStatut;
    public $nouveauStatut;

    public function __construct(Candidature $candidature, string $ancienStatut, string $nouveauStatut)
    {
        $this->candidature = $candidature;
        $this->ancienStatut = $ancienStatut;
        $this->nouveauStatut = $nouveauStatut;
    }
}