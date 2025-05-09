<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AdminMaterial extends Controller
{
    public function index()
    {
        try {
            $requests = Material::with('employee')
                ->select('materials.*', 'employes.nom as employe_nom', 'employes.prenom as employe_prenom')
                ->join('employes', 'materials.employe_id', '=', 'employes.id')
                ->get();
            return response()->json($requests);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve requests', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'statut' => 'required|in:approuve,rejete',
                'explication' => 'nullable|string|max:255',
            ]);

            $material = Material::findOrFail($id);

            if ($request->statut == 'refuse') {
                if (!$request->explication) {
                    return response()->json(['message' => 'insert your jestification'], 400);
                }
                $material->explication = $request->explication;
            }

            $material->statut = $request->statut;
            $material->save();

            return response()->json(['message' => 'nice']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update request', 'details' => $e->getMessage()], 500);
        }
    }
}


