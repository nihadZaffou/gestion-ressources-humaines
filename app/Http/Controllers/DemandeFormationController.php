<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationDemande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeFormationController extends Controller
{
    // EMPLOYÉ : envoyer une demande d'inscription à une formation
    public function envoyerDemande($formation_id, Request $request)
    {
        $employe_id = Auth::id(); // récupère l'ID de l'employé connecté
        $formation = Formation::findOrFail($formation_id); // vérifie si la formation existe

        // Vérifie si la formation est complète
        $demandesApprouvees = $formation->demandes()
            ->where('statut', 'approuvée')
            ->count();

        if ($demandesApprouvees >= $formation->places_disponibles) {
            return response()->json([
                'message' => "Désolé, cette formation est complète. Aucune place disponible."
            ], 400);
        }

        // Vérifie si une demande existe déjà
        $existe = FormationDemande::where('employe_id', $employe_id)
            ->where('formation_id', $formation_id)
            ->first();

        if ($existe) {
            return response()->json([
                'message' => "Vous avez déjà fait une demande pour cette formation."
            ], 409);
        }

        // Crée une nouvelle demande
        $demande = FormationDemande::create([
            'employe_id' => $employe_id,
            'formation_id' => $formation_id,
            'statut' => 'en attente',
        ]);

        return response()->json([
            'message' => 'Demande envoyée avec succès.',
            'data' => $demande
        ], 201);
    }

    // EMPLOYÉ : afficher ses propres demandes
    public function mesDemandes()
    {
        $employe_id = Auth::id();

        $demandes = FormationDemande::with('formation')
            ->where('employe_id', $employe_id)
            ->get();

        return response()->json($demandes);
    }

    // ADMIN : afficher toutes les demandes
    public function toutesLesDemandes()
    {
        $demandes = FormationDemande::with(['formation', 'employe'])->get();

        return response()->json($demandes);
    }

    // ADMIN : changer le statut d'une demande
    public function changerStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:approuvée,rejetée',
        ]);

        $demande = FormationDemande::findOrFail($id);
        $demande->statut = $request->statut;
        $demande->save();

        return response()->json(['message' => 'Statut mis à jour avec succès.']);
    }
}
