<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Absence;
use App\Models\Prime;
use App\Models\Remboursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FichePaieMailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FichePaieController extends Controller
{
    public function generateFichePaieForOne(Request $request, $id)
    {
        $request->validate([
            'mois' => 'required|date_format:m',
            'annee' => 'required|date_format:Y',
        ]);

        $employe = Employe::findOrFail($id);
        $salaireNet = $this->calculerSalaireNet($employe, $request->mois, $request->annee);

        $pdf = Pdf::loadView('pdf.fiche_paie', [
            'employe' => $employe,
            'mois' => $request->mois,
            'annee' => $request->annee,
            'salaire_net' => $salaireNet
        ]);

        Mail::to($employe->email)->send(new FichePaieMailable($employe, $pdf));

        return response()->json(['message' => 'Fiche de paie envoyée à ' . $employe->nom]);
    }

    public function generateFichePaieForAll(Request $request)
    {
        $request->validate([
            'mois' => 'required|date_format:m',
            'annee' => 'required|date_format:Y',
        ]);

        $employes = Employe::all();

        foreach ($employes as $employe) {
            $salaireNet = $this->calculerSalaireNet($employe, $request->mois, $request->annee);

            $pdf = Pdf::loadView('pdf.fiche_paie', [
                'employe' => $employe,
                'mois' => $request->mois,
                'annee' => $request->annee,
                'salaire_net' => $salaireNet
            ]);

            Mail::to($employe->email)->send(new FichePaieMailable($employe, $pdf));
        }

        return response()->json(['message' => 'Fiches de paie envoyées à tous les employés.']);
    }

    private function calculerSalaireNet($employe, $mois, $annee)
    {
        $salaireBase = $employe->salaire;

        $absences = Absence::where('employe_id', $employe->id)
            ->where('impact_salaire', true)
         ->whereMonth('date_debut', $mois)
        ->whereYear('date_debut', $annee)

            ->count();

        $penalites = $absences * 50;

     $primes = DB::table('prime_employe')
            ->join('primes', 'prime_employe.prime_id', '=', 'primes.id')
            ->where('prime_employe.employe_id', $employe->id)
            ->whereMonth('prime_employe.date_attribution', $mois)
            ->whereYear('prime_employe.date_attribution', $annee)
            ->sum('prime_employe.montant');

        $remboursements = Remboursement::where('employe_id', $employe->id)
            ->where('status', 'approuvé')
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->sum('montant');

        return $salaireBase - $penalites + $primes + $remboursements;
    }
}
