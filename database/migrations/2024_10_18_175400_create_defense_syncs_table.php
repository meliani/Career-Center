<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefenseSyncsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defense_syncs', function (Blueprint $table) {
            $table->id();
            $table->string('date_soutenance')->nullable();
            $table->string('heure')->nullable();
            $table->string('autorisation')->nullable();
            $table->string('lieu')->nullable();
            $table->string('id_pfe')->nullable();
            $table->string('nom_etudiant')->nullable();
            $table->string('filiere')->nullable();
            $table->string('encadrant_interne')->nullable();
            $table->string('examinateur_1')->nullable();
            $table->string('examinateur_2')->nullable();
            $table->text('remarques')->nullable();
            $table->string('convention_signee')->nullable();
            $table->string('accord_encadrant_interne')->nullable();
            $table->string('fiche_evaluation_entreprise')->nullable();
            $table->timestamps();

            $table->index('id_pfe')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defense_syncs');
    }
}
