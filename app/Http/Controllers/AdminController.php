<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offre;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET /api/admin/users
    public function indexUsers()
    {
        return response()->json(User::all(), 200);
    }

    // DELETE /api/admin/users/{user}
    public function destroyUser(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Compte supprimé.'], 200);
    }

    // PATCH /api/admin/offres/{offre}
    public function toggleOffre(Offre $offre)
    {
        // Active ou désactive l'offre
        $offre->update([
            'actif' => !$offre->actif
        ]);

        $message = $offre->actif ? 'Offre activée' : 'Offre désactivée';
        return response()->json(['message' => $message, 'offre' => $offre], 200);
    }
}