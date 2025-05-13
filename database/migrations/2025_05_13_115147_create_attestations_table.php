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
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('type_id')->constrained('attestationtypes')->onDelete('cascade');
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date_demande');
            $table->date('date_livraison');
            $table->enum('statut',['en attente' , 'accepte' , 'refuse'])->default('en attente');
            $table->string('pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attestations');
    }
};
