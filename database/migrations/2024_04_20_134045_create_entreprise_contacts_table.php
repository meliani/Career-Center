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
            $table->enum('category', \App\Enums\EntrepriseContactCategory::getArray());
            $table->string('years_of_interactions_with_students');
            $table->integer('number_of_bounces');
            $table->boolean('is_account_disabled');
            $table->timestamp('last_time_contacted')->nullable();
            $table->foreignId('last_year_id_supervised')->nullable()->constrained('years');
            $table->foreignId('first_year_id_supervised')->nullable()->constrained('years');
            $table->integer('interactions_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
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
