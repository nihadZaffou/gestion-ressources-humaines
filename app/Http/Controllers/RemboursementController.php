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
            ? $request->file('justification')->store('pdfs', 'public')
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
    public function supprimerDemande($id)
{
    $remboursement = Remboursement::where('id', $id)
        ->where('employe_id', Auth::id())
        ->where('status', 'en attente')
        ->first();

    if (!$remboursement) {
        return response()->json(["message" => "Demande introuvable ou déjà traitée."], 403);
    }

    $remboursement->delete();

    return response()->json(["message" => "Demande supprimée avec succès."]);
}
public function modifierDemande(Request $request, $id)
{
    $remboursement = Remboursement::where('id', $id)
        ->where('employe_id', Auth::id())
        ->where('status', 'en attente')
        ->first();

    if (!$remboursement) {
        return response()->json(["message" => "Demande introuvable ou déjà traitée."], 403);
    }

    $request->validate([
        'type' => 'sometimes|string',
        'montant' => 'sometimes|numeric|min:0',
        'justification' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    ]);

    if ($request->hasFile('justification')) {
        $remboursement->justification = $request->file('justification')->store('pdfs');
    }

    if ($request->has('type')) {
        $remboursement->type = $request->type;
    }

    if ($request->has('montant')) {
        $remboursement->montant = $request->montant;
    }

    $remboursement->save();

    return response()->json([
        'message' => 'Demande mise à jour avec succès.',
        'data' => $remboursement
    ]);
}
public function supprimerDemandeAdmin($id)
{
    $remboursement = Remboursement::find($id);

    if (!$remboursement) {
        return response()->json(['message' => 'Demande non trouvée.'], 404);
    }

    $remboursement->delete();

    return response()->json(['message' => 'Demande supprimée avec succès.']);
}

}
