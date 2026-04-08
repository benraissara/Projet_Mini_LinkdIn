<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offre extends Model
{   use HasFactory;
    protected $fillable=[
        'titre',
        'description',
    
        'localisation',
        'type',
        'actif',
        'user_id',
    ];
    // Une offre appartient à un recruteur (User)
    public function user(){
        return $this->belongsTo(User::class);
    }
    // Une offre peut avoir plusieurs candidatures
    public function candidatures(){
        return $this->hasMany(Candidature::class);
    }
}
