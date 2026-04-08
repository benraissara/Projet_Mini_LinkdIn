<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithPivotTable;

class Profil extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'user_id',
        'bio',
        'localisation',
        'disponible'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function competences(){
        return $this->belongsToMany(Competence::class,'competence_profils')
                    ->withPivot('niveau')
                    ->withTimestamps();
    }
}
