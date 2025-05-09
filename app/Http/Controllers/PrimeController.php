<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\EmployePrime;
use App\Models\Prime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimeController extends Controller
{
    public function index(Request $request)  {
       $Primes=Prime::all();
       return response()->json($Primes);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|min:0',
            'description' => 'nullable|string'

        ]);
    
        $prime = Prime::create($request->all());
    
        return response()->json([
            'message' => 'Prime créée avec succès',
            'data' => $prime
        ], 201);
    }
    public function updatePrime(Request $request, $id)
{
    $request->validate([
        'nom' => 'sometimes|required|string|max:255',
        'montant' => 'sometimes|required|numeric|min:0',
        'description' => 'nullable|string'
    ]);

    $prime = Prime::findOrFail($id);
    $prime->update($request->all());

    return response()->json([
        'message' => 'Prime modifiée avec succès',
        'data' => $prime
    ]);
}
public function deletePrime($id)
{
    $prime = Prime::findOrFail($id);
    $prime->delete();

    return response()->json([
        'message' => 'Prime supprimée avec succès'
    ]);
}

    public function attribuerPrime(Request $request)
{
    $request->validate([
        'employe_id' => 'required|exists:employes,id',
        'prime_id' => 'required|exists:primes,id',
        'date_attribution' => 'required|date',
         'montant' => 'required|numeric|min:0'
    ]);

    $employePrime = EmployePrime::create([
        'employe_id' => $request->employe_id,
        'prime_id' => $request->prime_id,
        'date_attribution' => $request->date_attribution,
         'montant' => $request->montant
    ]);

    return response()->json([
        'message' => 'Prime attribuée avec succès',
        'data' => $employePrime
    ]);
}
public function updateAttribution(Request $request, $id)
{
    $request->validate([
        'employe_id' => 'sometimes|required|exists:employes,id',
        'prime_id' => 'sometimes|required|exists:primes,id',
        'date_attribution' => 'sometimes|required|date',
        'montant' => 'sometimes|required|numeric|min:0',
        'remarque' => 'nullable|string'
    ]);

    $attribution = EmployePrime::findOrFail($id);
    $attribution->update($request->all());

    return response()->json([
        'message' => 'Attribution de prime mise à jour avec succès',
        'data' => $attribution
    ]);
}
public function deleteAttribution($id)
{
    $attribution = EmployePrime::findOrFail($id);
    $attribution->delete();

    return response()->json([
        'message' => 'Attribution de prime supprimée avec succès'
    ]);
}

public function primesEmploye($employe_id)
{
    $employe = Employe::with('primes')->findOrFail($employe_id);

    return response()->json([
        'employe' => $employe->nom,
        'primes' => $employe->primes
    ]);
}
public function mesPrimes()
{
    // Récupérer l'ID de l'employé connecté
    $employeId = auth()->user()->id;  // L'ID de l'employé actuellement connecté

    // Récupérer toutes les primes de l'employé en utilisant une requête directe
    $primes = DB::table('prime_employe')
                ->join('primes', 'prime_employe.prime_id', '=', 'primes.id')
                ->where('prime_employe.employe_id', $employeId)
                ->select('primes.*', 'prime_employe.date_attribution', 'prime_employe.montant', 'prime_employe.remarque')
                ->get();

    // Afficher ou retourner les primes
    return response()->json([
        'primes' => $primes
    ]);

}

}
