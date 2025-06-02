<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\EmployePrime;
use App\Models\Prime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimeController extends Controller
{
    
    public function index(Request $request) {
        $Primes = Prime::all();
        return response()->json($Primes);
    }
public function indexAttributions() {
    // Eager load related models for efficiency and to have data in frontend
    $attributions = EmployePrime::with(['employe:id,nom,prenom', 'prime:id,nom,montant']) // Select only needed columns
                                ->orderBy('date_attribution', 'desc')
                                ->get();
    return response()->json($attributions);
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
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'prime_id' => 'required|exists:primes,id',
        'date_attribution' => 'required|date',
        'montant' => 'required|numeric|min:0',
        'remarque' => 'nullable|string'
    ]);

    $employe = Employe::where('nom', $request->nom)
                      ->where('prenom', $request->prenom)
                      ->first();

    if (!$employe) {
        return response()->json([
            'message' => 'Employé non trouvé'
        ], 404);
    }

    $employePrime = EmployePrime::create([
        'employe_id' => $employe->id,
        'prime_id' => $request->prime_id,
        'date_attribution' => $request->date_attribution,
        'montant' => $request->montant,
        'remarque' => $request->remarque, // هادي
    ]);

    return response()->json([
        'message' => 'Prime attribuée avec succès à ' . $employe->nom . ' ' . $employe->prenom,
        'data' => $employePrime
    ]);
}

    public function updateAttribution(Request $request, $id)
    {
        $request->validate([
            'nom' => 'sometimes|required|string',
            'prenom' => 'sometimes|required|string',
            'prime_id' => 'sometimes|required|exists:primes,id',
            'date_attribution' => 'sometimes|required|date',
            'montant' => 'sometimes|required|numeric|min:0',
            'remarque' => 'nullable|string'
        ]);

        $attribution = EmployePrime::findOrFail($id);

        if ($request->has('nom') && $request->has('prenom')) {
            $employe = Employe::where('nom', $request->nom)
                              ->where('prenom', $request->prenom)
                              ->first();

            if (!$employe) {
                return response()->json([
                    'message' => 'Employé non trouvé'
                ], 404);
            }

            $attribution->employe_id = $employe->id;
        }

        if ($request->has('prime_id')) {
            $attribution->prime_id = $request->prime_id;
        }

        if ($request->has('date_attribution')) {
            $attribution->date_attribution = $request->date_attribution;
        }

        if ($request->has('montant')) {
            $attribution->montant = $request->montant;
        }

        if ($request->has('remarque')) {
            $attribution->remarque = $request->remarque;
        }

        $attribution->save();

        return response()->json([
            'message' => 'Attribution mise à jour avec succès',
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
    $employeId = auth()->user()->id;

    // Assuming 'prime_employe' is the correct pivot table name
    $primes = DB::table('prime_employe')
                ->join('primes', 'prime_employe.prime_id', '=', 'primes.id')
                ->where('prime_employe.employe_id', $employeId)
                ->select(
                    'primes.id as prime_definition_id', // ID of the prime definition
                    'primes.nom',                       // Name of the prime (e.g., "Prime de Performance")
                    'primes.description',               // General description of the prime
                    // 'primes.montant as montant_defaut_prime', // If you need the default amount of the prime definition
                    'prime_employe.id as attribution_id', // ID of this specific attribution
                    'prime_employe.date_attribution',
                    'prime_employe.montant as montant_attribue', // The actual amount attributed to the employee
                    'prime_employe.remarque'
                )
                ->orderBy('prime_employe.date_attribution', 'desc')
                ->get();

    return response()->json([
        'primes' => $primes
    ]);
}
}
