<?php

use App\Enums;
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
        //  check if table exists
        if (Schema::hasTable('students')) {
            return;
        }
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('title', 5)->nullable();
            $table->unsignedInteger('id_pfe')->nullable();
            $table->string('full_name', 191)->nullable();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_perso', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('cv', 191)->nullable();
            $table->string('lm', 191)->nullable();
            $table->string('photo', 191)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('level', Enums\StudentLevel::getValues())->nullable();
            $table->string('program', 10)->nullable();
            $table->tinyInteger('is_mobility')->nullable()->default(0);
            $table->string('abroad_school', 191)->nullable();
            $table->foreignId('year_id')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(0);
            $table->json('offers_viewed')->nullable();
            $table->date('graduated_at')->nullable();
            $table->tinyInteger('is_verified')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
