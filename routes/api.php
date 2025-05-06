<?php


use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\CongeValidationController;
use App\Http\Controllers\RemboursementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\EmployeAuthController;
use App\Http\Controllers\API\EmployeController;
use App\Http\Controllers\Employe\CongeController;

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