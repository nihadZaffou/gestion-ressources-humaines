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
        Schema::create('recrutements', function (Blueprint $table) {
            $table->id();
            $table->string('titre'); // Titre du recrutement
            $table->string('poste'); // Poste à pourvoir
            $table->text('descriptionPoste');// Description du poste
            $table->text('descriptionProfil'); // Description du profil recherché
            $table->date('date_debut'); // Date de début de la période de recrutement
            $table->date('date_fin'); // Date de fin de la période de recrutement
            $table->enum('statut', ['en cours', 'cloture'])->default('en cours'); // Statut du recrutement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recrutements');
    }
};
