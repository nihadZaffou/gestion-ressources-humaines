<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function store(Request $request){
        $validated=$request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'places_disponibles' => 'required|integer|min:1',
        ]);
        $formation=Formation::create($validated);
        return response()->json(['message' => 'Formation ajoutée avec succès', 'data' => $formation], 201);
    }
    public function index(Request $request){
        $formations=Formation::orderBy('date_debut','asc')->get();
        return response()->json($formations);
    }
    public function update(Request $request, $id)
{
    $formation = Formation::findOrFail($id);

    $request->validate([
        'titre' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'date_debut' => 'sometimes|required|date',
        'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
        'places_disponibles'=>'required',
    ]);

    $formation->update($request->all());

    return response()->json(['message' => 'Formation mise à jour', 'data' => $formation]);
}
public function destroy($id)
{
    $formation = Formation::findOrFail($id);
    $formation->delete();

    return response()->json(['message' => 'Formation supprimée avec succès']);
}

}
