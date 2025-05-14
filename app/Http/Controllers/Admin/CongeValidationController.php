<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conge;
use Illuminate\Http\Request;

class CongeValidationController extends Controller
{
    public function index(){
        $conges = Conge::with('employe:id,nom,prenom')->get();
        return response()->json($conges);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'statut'  => 'required|in:approuve,rejete',
            'explication' => 'nullable|string|max:255', // Validation pour l'explication
        ]);
        $conge = Conge::findOrFail($id);
        // Si l'admin refuse, on ajoute une explication
        if ($request->statut == 'rejete') {
            if (!$request->explication) {
                return response()->json(['message' => 'Une explication est requise pour un refus.'], 400);
            }
            $conge->explication = $request->explication;
        }
    
        // Mise à jour du statut
        $conge->statut = $request->statut;
        $conge->save();
    
        return response()->json(['message' => 'Statut mis à jour avec succès.']);
    }
    
    
}
