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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
             $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('remboursements', 10, 2)->default(0);
            $table->decimal('absences', 10, 2)->default(0); // Montant retenu
            $table->decimal('salaire_net', 10, 2);
            $table->integer('mois');
            $table->integer('annee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
