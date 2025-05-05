<?php

namespace App\Http\Controllers\Employe;

use App\Http\Controllers\Controller;
use App\Models\Conge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CongeController extends Controller
{
   // ✅ Voir ses demandes
   public function index()
   {
       $employeId = Auth::id();
       $conges = Conge::where('employe_id', $employeId)->get();
       return response()->json($conges);
   }

   // ✅ Créer une demande
   public function store(Request $request)
{
    $request->validate([
        'date_debut' => 'required|date|after_or_equal:today',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'motif' => 'nullable|string|max:255'
    ]);

    $employe = Auth::user(); 
    if (!$employe) {
        return response()->json(['message' => 'Vous êtes déconnecté.'], 401);
    }

    $conge = new Conge();
    $conge->employe_id = $employe->id;
    $conge->date_debut = $request->date_debut;
    $conge->date_fin = $request->date_fin;
    $conge->motif = $request->motif;
    $conge->statut = 'en_attente';
    $conge->save();

    return response()->json(['message' => 'Demande envoyée avec succès.'], 201);
}

   // ✅ Modifier (seulement si statut = en attente)
   public function update(Request $request, $id)
   {
       $conge = Conge::where('id', $id)->where('employe_id', Auth::id())->firstOrFail();

       if ($conge->statut !== 'en attente') {
           return response()->json(['message' => 'Impossible de modifier une demande déjà traitée.'], 403);
       }

       $request->validate([
           'date_debut' => 'required|date',
           'date_fin' => 'required|date|after_or_equal:date_debut',
           'motif' => 'nullable|string|max:255'
       ]);

       $conge->update([
           'date_debut' => $request->date_debut,
           'date_fin' => $request->date_fin,
           'motif' => $request->motif
       ]);

       return response()->json(['message' => 'Demande modifiée.']);
   }

   // ✅ Supprimer (seulement si statut = en attente)
   public function destroy($id)
   {
       $conge = Conge::where('id', $id)->where('employe_id', Auth::id())->firstOrFail();

       if ($conge->statut !== 'en attente') {
           return response()->json(['message' => 'Impossible de supprimer une demande déjà traitée.'], 403);
       }

       $conge->delete();

       return response()->json(['message' => 'Demande supprimée.']);
   }
}    
