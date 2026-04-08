<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenceProfil extends Model
{
    use HasFactory;
    protected $fillable = [
        'niveau',
        'profil_id',
        'competence_id'
    ];
}
