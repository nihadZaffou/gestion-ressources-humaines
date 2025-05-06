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
            Schema::create('absences', function (Blueprint $table) {
                $table->id(); 
                $table->unsignedBigInteger('employe_id');
                $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
                $table->date('date_debut');
                $table->date('date_fin')->nullable(); 
                $table->string('motif')->nullable();
                $table->string('justificatif')->nullable();
                $table->boolean('justifiee')->default(false);
                $table->boolean('cree_par_admin')->default(false);
                $table->boolean('impact_salaire')->default(false);
    
                $table->timestamps();
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('absences');
        }
};
