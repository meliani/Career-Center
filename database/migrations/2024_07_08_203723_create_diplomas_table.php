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
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Matr
            $table->string('cne')->unique(); // CNE
            $table->string('cin')->unique(); // CIN
            $table->string('first_name'); // Prénom
            $table->string('last_name'); // Nom
            $table->string('full_name'); // Nom & Prénom
            $table->string('last_name_ar'); // Nom_Ar
            $table->string('first_name_ar'); // Prenom_Ar
            $table->string('birth_place_ar'); // Lieu_Ar
            $table->string('birth_place_fr'); // Lieu_Fr
            $table->date('birth_date'); // DATE DE NAISSANCE
            $table->string('nationality'); // Nationalité
            $table->string('council'); // Conseil
            $table->string('program_code'); // code filière
            $table->string('assigned_program'); // Filière Affectée
            $table->string('program_tifinagh'); // Filière Tifinagh
            $table->string('program_english'); // Filière Anglais
            $table->string('program_arabic'); // Filière Arabe
            $table->string('qr_code')->nullable(); // qr code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diplomas');
    }
};
