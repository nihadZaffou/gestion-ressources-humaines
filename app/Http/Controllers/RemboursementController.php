<?php

namespace App\Http\Controllers;

use App\Models\Remboursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemboursementController extends Controller
{
    // Employé : afficher ses propres demandes
    public function afficherMesDemandes()
    {
        $employeId = Auth::id();
        $demandes = Remboursement::where('employe_id', $employeId)->get();

        if ($demandes->isEmpty()) {
            return response()->json(["message" => "Aucune demande n'a été trouvée."], 404);
        }

        return response()->json($demandes);
    }

    // Employé : effectuer une demande
    public function effectuerDemande(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'justification' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $justificationPath = $request->hasFile('justification')
            ? $request->file('justification')->store('justifications')
            : null;

        $remboursement = Remboursement::create([
            'employe_id' => Auth::id(),
            'type' => $request->type,
            'montant' => $request->montant,
            'justification' => $justificationPath,
            'status' => 'en attente',
        ]);

        return response()->json([
            "message" => "Demande envoyée avec succès.",
            "data" => $remboursement
        ], 201);
    }

    // Admin : voir toutes les demandes
    public function toutesDemandes()
    {
        $remboursements = Remboursement::with('employe')->get(); // Optionnel : inclure les infos de l'employé
        return response()->json($remboursements);
    }

    // Admin : mettre à jour le statut
    public function mettreAJourStatut(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approuvé,refusé',
        ]);

        $remboursement = Remboursement::findOrFail($id);
        $remboursement->status = $request->status;
        $remboursement->save();

        return response()->json([
            'message' => 'Statut de la demande mis à jour avec succès.',
            'data' => $remboursement
        ]);
    }
}
