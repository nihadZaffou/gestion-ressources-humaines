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

    // Calcul de la durée de la nouvelle demande
    $nouvelleDuree = \Carbon\Carbon::parse($request->date_fin)
        ->diffInDays(\Carbon\Carbon::parse($request->date_debut)) + 1;

    // Total des jours approuvés dans l'année en cours
    $annee = \Carbon\Carbon::now()->year;
    $joursDejaPris = Conge::where('employe_id', $employe->id)
        ->where('statut', 'approuve')
        ->whereYear('date_debut', $annee)
        ->get()
        ->reduce(function ($total, $conge) {
            return $total + \Carbon\Carbon::parse($conge->date_fin)
                ->diffInDays(\Carbon\Carbon::parse($conge->date_debut)) + 1;
        }, 0);

    $soldeRestant = 30 - $joursDejaPris;

    // Vérification du dépassement
    if (($joursDejaPris + $nouvelleDuree) > 30) {
        return response()->json([
            'message' => 'Vous avez dépassé le quota de 30 jours de congé pour cette année.',
            'jours_utilises' => $joursDejaPris,
            'jours_demandes' => $nouvelleDuree,
            'solde_restant' => max(0, $soldeRestant)
        ], 403);
    }

    // Enregistrement de la demande
    $conge = new Conge();
    $conge->employe_id = $employe->id;
    $conge->date_debut = $request->date_debut;
    $conge->date_fin = $request->date_fin;
    $conge->motif = $request->motif;
    $conge->statut = 'en_attente';
    $conge->save();

    return response()->json([
        'message' => 'Demande envoyée avec succès.',
        'jours_demandes' => $nouvelleDuree,
        'jours_utilises' => $joursDejaPris,
        'solde_restant' => $soldeRestant - $nouvelleDuree
    ], 201);
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
