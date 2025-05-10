<?php

namespace App\Http\Controllers;

use App\Models\Recrutement;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RecrutementController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'poste' => 'required|string|max:255',
                'descriptionPoste' => 'nullable|string',
                'descriptionProfil' => 'nullable|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'statut' => 'nullable|string|in:en cours,cloture',
            ]);

            // Set default value for 'statut' if not provided
            $validated['statut'] = $validated['statut'] ?? 'en cours';
            

            $recrutement = Recrutement::create($validated);

            return response()->json(['message' => 'Recrutement ajouté avec succès', 'data' => $recrutement], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $recrutements = Recrutement::orderBy('date_debut', 'asc')->get();
            return response()->json($recrutements);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the recruitment record
            $recrutement = Recrutement::findOrFail($id);

            // Validate the request
            $validatedData = $request->validate([
                'titre' => 'sometimes|required|string|max:255',
                'poste' => 'sometimes|required|string|max:255',
                'descriptionPoste' => 'nullable|string',
                'descriptionProfil' => 'nullable|string',
                'date_debut' => 'sometimes|required|date',
                'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
                'statut' => 'sometimes|required|string|in:en cours,cloture',
            ]);

            // Update the recruitment record
            $recrutement->update($validatedData);

            return response()->json(['message' => 'Recrutement mis à jour', 'data' => $recrutement]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Recrutement introuvable', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $recrutement = Recrutement::findOrFail($id);
            $recrutement->delete();

            return response()->json(['message' => 'Recrutement supprimé avec succès']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Recrutement introuvable', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }
}
