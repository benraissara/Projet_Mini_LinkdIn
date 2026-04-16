<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OffreController extends Controller
{
    

    public function show($id) 
    {
        $offre = Offre::findOrFail($id);
        
        return response()->json([
            'message' => 'Offre trouvée avec succès',
            'data' => $offre
        ], 200);
    }

    public function update(Request $request, $id) // Changé Offre $offre en $id
    {
        $offre = Offre::findOrFail($id);

        if (Auth::id() !== $offre->user_id) {
            return response()->json(['message' => 'Non autorisé - 403'], 403);
        }

        $validate = $request->validate([
            'titre' => 'required|max:255',
            'description' => 'required',
            'localisation' => 'required',
            'type' => 'required|in:CDI,CDD,Stage'
        ]);

        $offre->update($validate);

        return response()->json([
            'message' => 'Offre mise à jour',
            'data' => $offre
        ]);
    }

    public function destroy($id) 
    {
        $offre = Offre::findOrFail($id);

        if (Auth::id() !== $offre->user_id) {
            return response()->json(['message' => 'Non autorisé - 403'], 403);
        }

        $offre->delete();

        return response()->json([
            'message' => 'Offre supprimée'
        ]);
    }

    
    public function index(Request $request)
    {
        $query = Offre::query()->where('actif', true);
        if ($request->has('localisation')) { $query->where('localisation', $request->localisation); }
        if ($request->has('type')) { $query->where('type', $request->type); }

        $offres = $query->latest()->paginate(10);
        return response()->json($offres, 200);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'titre' => 'required|max:255',
            'description' => 'required',
            'localisation' => 'required',
            'type' => 'required|in:CDI,CDD,Stage'
        ]);

        $validate['user_id'] = Auth::id();
        $offre = Offre::create($validate);

        return response()->json([
            'message' => 'Offre créée avec succès',
            'data' => $offre
        ], 201);
    }
}