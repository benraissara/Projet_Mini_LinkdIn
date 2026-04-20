<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offre;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function indexUsers()
    {
        return response()->json(User::all(), 200);
    }

    
    public function destroyUser(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Compte supprimé.'], 200);
    }

    
    public function toggleOffre(Offre $offre)
    {
        
        $offre->update([
            'actif' => !$offre->actif
        ]);

        $message = $offre->actif ? 'Offre activée' : 'Offre désactivée';
        return response()->json(['message' => $message, 'offre' => $offre], 200);
    }
}