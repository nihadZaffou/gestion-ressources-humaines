<?php

use App\Http\Controllers\Admin\CongeValidationController;
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
