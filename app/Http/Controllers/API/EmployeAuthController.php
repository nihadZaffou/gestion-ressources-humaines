<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class EmployeAuthController extends Controller
{
    // Méthode pour l'authentification de l'employé
    public function login(Request $request)
{
    // Validation des données d'entrée
    $validator = validator::make($request->all(), [
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Vérifier si l'employé existe avec l'email donné
    $employe = Employe::where('email', $request->email)->first();

    // Si l'employé n'existe pas ou si le mot de passe est incorrect
    if (!$employe || !Hash::check($request->password, $employe->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    // Création du token d'authentification
    $token = $employe->createToken('EmployeApp')->plainTextToken;

    // Retourner le token à l'employé
    return response()->json([
        'message' => 'Connexion réussie',
        'token' => $token
    ], 200);
}

    // Méthode pour la déconnexion de l'employé
    public function logout(Request $request)
    {
    
            // Invalide le token de l'utilisateur authentifié
            $request->user()->currentAccessToken()->delete();
    
            return response()->json(['message' => 'Déconnexion réussie.']);
        
    }

    // Méthode pour obtenir les détails de l'employé connecté
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}


