<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index()
    {
        try {
            // Récupérer tous les matériaux
            $materials = Material::all();
            return response()->json($materials);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve materials', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Récupérer un matériau spécifique
            $material = Material::findOrFail($id);
            return response()->json($material);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Material not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve material', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Valider la requête
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'motif' => 'required|string|max:255',
                'quantite' => 'required|integer',
            ]);

            // Ajouter automatiquement l'ID de l'utilisateur authentifié
            $validatedData['employe_id'] = Auth::id();

            // Définir un statut par défaut
            $validatedData['statut'] = 'en_attente';

            // Créer un nouveau matériau
            $material = Material::create($validatedData);
            return response()->json($material, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create material', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Valider la requête
            $validatedData = $request->validate([
                'nom' => 'sometimes|required|string|max:255',
                'motif' => 'sometimes|required|string|max:255',
                'quantite' => 'sometimes|required|integer',
            ]);

            // Ajouter automatiquement l'ID de l'utilisateur authentifié
            $validatedData['employe_id'] = Auth::id();

            // Mettre à jour le matériau
            $material = Material::findOrFail($id);
            $material->update($validatedData);
            return response()->json($material);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Material not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update material', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Supprimer le matériau
            $material = Material::findOrFail($id);
            $material->delete();
            return response()->json(['message' => 'Material deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Material not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete material', 'details' => $e->getMessage()], 500);
        }
    }
}
