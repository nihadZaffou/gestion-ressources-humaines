<?php
namespace App\Http\Controllers;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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
        'justifiee' => 'required', // plus 'boolean' ici, on va le convertir manuellement
        'justificatif' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    ]);

    $absence = Absence::findOrFail($id);

    // Convertir manuellement en booléen
    $justifiee = filter_var($request->justifiee, FILTER_VALIDATE_BOOLEAN);

    $absence->justifiee = $justifiee;
    $absence->impact_salaire = !$justifiee;

    if ($request->hasFile('justificatif')) {
        $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
        $absence->justificatif = $justificatifPath;
    }

    $absence->save();

    return response()->json([
        'message' => 'Justification mise à jour avec succès.',
        'data' => $absence
    ]);
}



public function update(Request $request, $id)
{
    $request->validate([
        'date_debut' => 'required|date',
        'date_fin' => 'nullable|date|after_or_equal:date_debut',
        'motif' => 'required|string',
        'justificatif' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    ]);

    $absence = Absence::findOrFail($id);
    $employeId = auth()->user()->id;
    if ($absence->employe_id !== $employeId) {
        return response()->json(['message' => 'Vous ne pouvez pas modifier cette absence.'], 403);
    }
    $createdAt = Carbon::parse($absence->created_at);
    if ($createdAt->diffInHours(now()) > 48) {
        return response()->json(['message' => 'Vous ne pouvez plus modifier votre absence après 48 heures.'], 422);
    }
    $absence->date_debut = $request->date_debut;
    $absence->date_fin = $request->date_fin ?? $request->date_debut;
    $absence->motif = $request->motif;
    if ($request->hasFile('justificatif')) {
        $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
        $absence->justificatif = $justificatifPath;
        $absence->justifiee = true;
    }
    $absence->impact_salaire = $absence->justifiee ? false : true;

    $absence->save();

    return response()->json([
        'message' => 'Demande d\'absence mise à jour avec succès.',
        'data' => $absence
    ]);
}

public function supprimer($id)
{

    if (!auth()->check()) {
        return response()->json(['message' => 'Vous devez être connecté pour supprimer une absence.'], 401);
    }
    $absence = Absence::findOrFail($id);
    $employeId = auth()->user()->id;
    if ($absence->employe_id !== $employeId) {
        return response()->json(['message' => 'Vous ne pouvez pas supprimer cette absence.'], 403);
    }
    $createdAt = Carbon::parse($absence->created_at);
    if ($createdAt->diffInHours(now()) > 48) {
        return response()->json(['message' => 'Vous ne pouvez plus supprimer cette absence après 48 heures.'], 422);
    }
    $absence->delete();

    return response()->json(['message' => 'Demande d\'absence supprimée avec succès.']);
}
}

