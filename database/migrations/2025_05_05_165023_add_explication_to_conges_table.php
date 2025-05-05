<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('conges', function (Blueprint $table) {
            // Ajout du champ explication, nullable car non obligatoire pour une demande approuvée
            $table->text('explication')->nullable()->after('statut'); // Ajoute après la colonne statut
        });
    }

    /**
     * Annuler la migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conges', function (Blueprint $table) {
            // Suppression de la colonne explication si on annule la migration
            $table->dropColumn('explication');
        });
    }
};
