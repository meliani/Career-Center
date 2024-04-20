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
        Schema::create('entreprise_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company');
            $table->string('position');
            $table->string('alumni_promotion');
            $table->string('category');
            $table->string('years_of_interactions_with_students');
            $table->integer('number_of_bounces');
            $table->boolean('is_account_disabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_contacts');
    }
};
