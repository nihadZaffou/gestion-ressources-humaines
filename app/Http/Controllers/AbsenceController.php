<?php
namespace App\Http\Controllers;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AbsenceController extends Controller
{
    public function demanderAbsence(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'motif' => 'required|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);
    
        $justificatifPath = null;
        if ($request->hasFile('justificatif')) {
            $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
        }
    
        $absence = Absence::create([
            'employe_id' => auth()->user()->id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin ?? $request->date_debut,
            'motif' => $request->motif,
            'justificatif' => $justificatifPath,
            'justifiee' => $justificatifPath ? true : false,
            'cree_par_admin' => false,
            'impact_salaire' => $justificatifPath ? false : true,
        ]);
    
        return response()->json([
            'message' => 'Demande d\'absence envoyée avec succès.',
            'data' => $absence
        ], 201);
    }

    public function mesAbsences()
    {
        $employeId = Auth::id();
        $absences = Absence::where('employe_id', $employeId)->get();

        return response()->json($absences);
    }
    public function toutesAbsences()
    {
        $absences = Absence::with('employe:id,nom,email')->get();

        return response()->json($absences);
    }
    public function ajouterAbsencePourEmploye(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        $absence = new Absence();
        $absence->employe_id = $request->employe_id;
        $absence->date_debut = $request->date_debut;
        $absence->date_fin = $request->date_fin;
        $absence->motif = $request->motif;
        $absence->cree_par_admin = true;

        $absence->save();

        return response()->json(['message' => 'Absence ajoutée par l\'admin']);
    }
    public function validerJustification($id, Request $request)
    {
        $request->validate([
            'justifiee' => 'required|boolean',
        ]);

        $absence = Absence::findOrFail($id);
        $absence->justifiee = $request->justifiee;
        $absence->impact_salaire = !$request->justifiee; // si non justifiée → impact

        $absence->save();

        return response()->json(['message' => 'Statut de justification mis à jour']);
    }
}

