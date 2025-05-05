<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeController extends Controller
{
    public function index()
    {
        // Récupérer tous les employés
        $Employes = Employe::all();
        return response()->json(['message' => $Employes], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employes',
            'telephone' => 'required',
            'poste' => 'required|string|max:255',
            'salaire' => 'required|numeric',
            'date_entree' => 'required|date',
            'password' => 'required|string|min:6',
        ]);

        // Hashage manuel du mot de passe
        $validatedData['password'] = Hash::make($request->password);

        $employe = Employe::create($validatedData);

        return response()->json([
            'message' => 'Employé ajouté avec succès.',
            'employe' => $employe
        ], 201);
    }
    public function show($id)
    {
        // Récupérer un employé par son ID
        $Employe = Employe::find($id);
        return $Employe ? $Employe : response()->json(["message" => 'Employé introuvable'], 404);
    }

    public function update(Request $request, $id)
{
    $employe = Employe::find($id);

    if (!$employe) {
        return response()->json(['error' => 'Employé non trouvé'], 404);
    }

    $validatedData = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:employes,email,' . $id,
        'telephone' => 'required',
        'poste' => 'required|string|max:255',
        'salaire' => 'required|numeric',
        'date_entree' => 'required|date',
        'password' => 'nullable|string|min:6', // facultatif
    ]);

    // Si un nouveau mot de passe est fourni, on le hash
    if (!empty($validatedData['password'])) {
        $validatedData['password'] = Hash::make($validatedData['password']);
    } else {
        // Si le mot de passe n'est pas fourni, on l’enlève pour ne pas écraser l'ancien
        unset($validatedData['password']);
    }
    $employe->update($validatedData);
    return response()->json([
        'message' => 'Employé mis à jour avec succès',
        'employe' => $employe
    ], 200);
}
    public function destroy($id)
    {
        // Supprimer un employé par son ID
        $employe = Employe::find($id);
        if (!$employe) {
            return response()->json(['message' => 'Employé non trouvé'], 404);
        }

        // Suppression de l'employé
        $employe->delete();
        return response()->json(['message' => 'Employé supprimé avec succès'], 200);
    }
}

