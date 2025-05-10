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
        Schema::create('fiche_de_paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
$table->integer('mois');
$table->integer('annee');
$table->decimal('salaire_base', 10, 2);
$table->decimal('primes', 10, 2);
$table->decimal('remboursements', 10, 2);
$table->integer('absences');
$table->decimal('penalite', 10, 2);
$table->decimal('salaire_net', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
