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
        Schema::table('employes', function (Blueprint $table) {
            $table->string('password')->nullable(); // Ajoute le champ password
        });
    }
    
    public function down()
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn('password'); // En cas de rollback, supprime le champ password
        });
    }
};
