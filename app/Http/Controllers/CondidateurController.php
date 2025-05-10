<?php

namespace App\Http\Controllers;

use App\Models\Condidateur;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CondidateurController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telephone' => 'required|string|max:20',
                'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'lettre_motivation' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'recrutement_id' => 'required|exists:recrutements,id',
                'statut' => 'nullable|in:en attente,accepte,rejete',
                'justification' => 'nullable|string'
            ]);

            // Set default value for 'statut' if not provided
            $validated['statut'] = $validated['statut'] ?? 'en attente';

            // Store the CV and cover letter
            if ($request->hasFile('cv')) {
                $validated['cv'] = $request->file('cv')->store('cvs');
            }
            if ($request->hasFile('lettre_motivation')) {
                $validated['lettre_motivation'] = $request->file('lettre_motivation')->store('lettres_motivation');
            }

            // Create the condidateur record
            $condidateur = Condidateur::create($validated);

            return response()->json(['message' => 'Condidateur ajouté avec succès', 'data' => $condidateur], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            $condidateurs = Condidateur::orderBy('created_at', 'desc')->get();
            return response()->json($condidateurs);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        try {
            $condidateur = Condidateur::findOrFail($id);
            return response()->json($condidateur);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Condidateur non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            // Find the condidateur record
            $condidateur = Condidateur::findOrFail($id);

            // Validate the request
            $validatedData = $request->validate([
                'statut' => 'required|in:accepte,rejete',
                'justification' => 'required_if:statut,rejete|string'
            ]);

            // Update only the statut and justification fields
            $condidateur->statut = $validatedData['statut'];
            if ($validatedData['statut'] === 'rejete') {
                $condidateur->justification = $validatedData['justification'];
            } else {
                $condidateur->justification = null; // Clear justification if not rejected
            }

            // Save the updated condidateur record
            $condidateur->save();

            return response()->json(['message' => 'Condidateur mis à jour avec succès', 'data' => $condidateur], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Condidateur non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $condidateur = Condidateur::findOrFail($id);
            $condidateur->delete();

            return response()->json(['message' => 'Condidateur supprimé avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Condidateur non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
}
