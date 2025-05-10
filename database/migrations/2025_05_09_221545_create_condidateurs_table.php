<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('condidateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du candidat
            $table->string('prenom'); // Prénom du candidat
            $table->string('email')->unique(); // Email du candidat
            $table->string('telephone'); // Numéro de téléphone du candidat
            $table->string('cv'); // Chemin vers le CV du candidat
            $table->string('lettre_motivation'); // Chemin vers la lettre de motivation du candidat
            $table->foreignId('recrutement_id')->constrained('recrutements')->onDelete('cascade'); // ID du recrutement associé
            $table->enum('statut', ['en attente', 'accepte', 'rejete'])->default('en attente'); // Statut du candidat
            $table->text('justification')->nullable(); // Justification du statut (si applicable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condidateurs');
    }
};
