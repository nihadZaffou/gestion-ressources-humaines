<?php


use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\CongeValidationController;
use App\Http\Controllers\AdminMaterial;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\RemboursementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\EmployeAuthController;
use App\Http\Controllers\API\EmployeController;
use App\Http\Controllers\DemandeFormationController;
use App\Http\Controllers\Employe\CongeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PrimeController;

//admin 
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
//admin apres auth
Route::middleware(['auth:admin'])->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);
    Route::post('/admin/refresh', [AdminAuthController::class, 'refresh']);
});
//admin gere employe
Route::middleware('auth:admin')->group(function () {
    Route::get('/employes', [EmployeController::class, 'index']);       // Liste tous les employés
    Route::post('/employes', [EmployeController::class, 'store']);      // Ajouter un employé
    Route::get('/employes/{id}', [EmployeController::class, 'show']);   // Afficher un seul employé
    Route::put('/employes/{id}', [EmployeController::class, 'update']); // Modifier un employé
    Route::delete('/employes/{id}', [EmployeController::class, 'destroy']); // Supprimer un employé
});
//employe auth
Route::prefix('employe')->group(function () {
    Route::post('/login', [EmployeAuthController::class, 'login'])->name('login');
    Route::middleware('auth:employe')->group(function () {
        Route::get('/profile', [EmployeAuthController::class, 'profile']);
        Route::post('/logout', [EmployeAuthController::class, 'logout']);
    });
});
//admin conge
Route::middleware('auth:admin')->group(function () {
    Route::put('/admin/conges/{id}', [CongeValidationController::class, 'update']);
    Route::get('/admin/conges', [CongeValidationController::class, 'index']);
});
//employe conge
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employe/conges', [CongeController::class, 'store']);
    Route::get('/employe/conges', [CongeController::class, 'index']);
});
//Abscence

// Routes pour les absences des employés
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employe/absences', [AbsenceController::class, 'demanderAbsence']);  // Demander une absence
    Route::get('/employe/absences', [AbsenceController::class, 'mesAbsences']);  // Afficher les absences d'un employé
});

// Routes pour l'admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/absences', [AbsenceController::class, 'toutesAbsences']);  // Afficher toutes les absences
    Route::post('/admin/absences', [AbsenceController::class, 'ajouterAbsencePourEmploye']);  // Ajouter une absence pour un employé
    Route::put('/admin/absences/{id}/justification', [AbsenceController::class, 'validerJustification']);  // Valider la justification d'une absence
});

// Remboursements

// Routes pour les employés
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employe/remboursements', [RemboursementController::class, 'effectuerDemande']); 
    Route::get('/employe/remboursements', [RemboursementController::class, 'afficherMesDemandes']);
});

// Routes pour l'admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/remboursements', [RemboursementController::class, 'toutesDemandes']);
    Route::put('/admin/remboursements/{id}', [RemboursementController::class, 'mettreAJourStatut']); 
});
//employe
Route::middleware('auth:sanctum')->group(function () {
    // Liste des formations disponibles
    Route::get('/employe/formations', [FormationController::class, 'index']);

    // Faire une demande de participation à une formation
    Route::post('/employe/formations/{id}/demande', [DemandeFormationController::class, 'envoyerDemande']);

    // Voir mes demandes de formation
    Route::get('/employe/demandes-formations', [DemandeFormationController::class, 'mesDemandes']);
});
//ADMIN
Route::middleware('auth:admin')->group(function () {
    // Créer une formation
    Route::post('/admin/formations', [FormationController::class, 'store']);

    // Lister toutes les formations (peut être utilisé en admin panel)
    Route::get('/admin/formations', [FormationController::class, 'index']);

    // Modifier une formation
    Route::put('/admin/formations/{id}', [FormationController::class, 'update']);

    // Supprimer une formation
    Route::delete('/admin/formations/{id}', [FormationController::class, 'destroy']);

    // Voir toutes les demandes des employés
    Route::get('/admin/demandes-formations', [DemandeFormationController::class, 'toutesDemandes']);

    // Valider ou rejeter une demande de formation
    Route::put('/admin/demandes-formations/{id}', [DemandeFormationController::class, 'changerStatut']);
});

// Admin routes pour la gestion des primes
Route::middleware('auth:admin')->group(function () {
    //  des primes
    Route::get('/admin/primes', [PrimeController::class, 'index']);
    Route::post('/admin/primes', [PrimeController::class, 'store']);
    Route::put('/admin/primes/{id}', [PrimeController::class, 'updatePrime']);
    Route::delete('/admin/primes/{id}', [PrimeController::class, 'deletePrime']);
    
    // Attribuer une prime à un employé
    Route::post('/admin/primes/attribuer', [PrimeController::class, 'attribuerPrime']);
    Route::put('admin/prime-attributions/{id}', [PrimeController::class, 'updateAttribution']);
    Route::delete('admin/prime-attributions/{id}', [PrimeController::class, 'deleteAttribution']);
   
    //  Liste des primes pour un employé spécifique
    Route::get('/admin/employe/{employe_id}/primes', [PrimeController::class, 'primesEmploye']);
});
// Employe routes pour la gestion des primes
Route::middleware('auth:sanctum')->group(function () {
    // Afficher les primes de l'employé connecté
    Route::get('/employe/mes-primes', [PrimeController::class, 'mesPrimes']);
});
// Routes pour la gestion des matériaux
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('material', MaterialController::class);
});
// Routes pour l'admin
Route::middleware('auth:admin')->group(function () {
    Route::apiResource('admin/material',AdminMaterial::class);
});