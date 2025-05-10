<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Employe;
use App\Models\EmployePrime;
use App\Models\FicheDePaie;
use App\Models\Remboursement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FichePaie extends Controller
{
    // Admin : Générer une fiche de paie
    public function genererFichePaie(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer',
        ]);

        $employe_id = $request->employe_id;
        $mois = $request->mois;
        $annee = $request->annee;

        $employe = Employe::findOrFail($employe_id);
        $salaire_base = $employe->salaire;

        // ✅ Primes du mois
        $primes = EmployePrime::where('employe_id', $employe_id)
            ->whereMonth('date_attribution', $mois)
            ->whereYear('date_attribution', $annee)
            ->sum('montant');

        // ✅ Remboursements du mois approuvés
        $remboursements = Remboursement::where('employe_id', $employe_id)
            ->where('status', 'approuve')
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->sum('montant');

        // ✅ Absences non justifiées impactant le salaire du mois
        $absences = Absence::where('employe_id', $employe_id)
            ->where('justifiee', false)
            ->where('impact_salaire', true)
            ->whereMonth('date_debut', $mois)
            ->whereYear('date_debut', $annee)
            ->count();

        // Montant pénalité pour absences
        $montant_penalite = $absences * 200;

        // Calcul du salaire net
        $salaire_net = $salaire_base + $primes + $remboursements - $montant_penalite;

        // ✅ Création de la fiche
        $fiche = FicheDePaie::create([
            'employe_id' => $employe_id,
            'mois' => $mois,
            'annee' => $annee,
            'salaire_base' => $salaire_base,
            'primes' => $primes,
            'remboursements' => $remboursements,
            'absences' => $absences,
            'penalite' => $montant_penalite,
            'salaire_net' => $salaire_net,
        ]);

        return response()->json($fiche, 201);
    }

    // Télécharger une fiche en PDF
    public function telechargerPDF($id)
    {
        $fiche = FicheDePaie::with('employe')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.fiche_paie', [
            'fiche' => $fiche,
            'employe' => $fiche->employe
        ]);

        return $pdf->download("fiche_paie_{$fiche->employe->nom}_{$fiche->mois}_{$fiche->annee}.pdf");
    }

    // Voir toutes les fiches d'un employé (Admin ou Employé)
    public function fichesEmploye($employe_id)
    {
        $fiches = FicheDePaie::where('employe_id', $employe_id)->get();
        return response()->json($fiches);
    }

    // Voir une fiche par ID
    public function show($id)
    {
        $fiche = FicheDePaie::with('employe')->findOrFail($id);
        return response()->json($fiche);
    }

    // Employé connecté consulte ses fiches
    public function mesFiches()
    {
        $user = auth()->user();
        $employe_id = $user->employe->id ?? null;
        if (!$employe_id) {
            return response()->json(['message' => 'Employé non trouvé'], 404);
        }

        $fiches = FicheDePaie::where('employe_id', $employe_id)->get();
        return response()->json($fiches);
    }
}
