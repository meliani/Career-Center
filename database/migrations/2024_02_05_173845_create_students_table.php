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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('title', 5)->nullable();
            $table->unsignedInteger('pin')->nullable();
            $table->string('full_name', 191)->nullable();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('email_perso', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('cv', 191)->nullable();
            $table->string('lm', 191)->nullable();
            $table->string('photo', 191)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('level', Enums\StudentLevel::getArray())->nullable();
            $table->string('program', 10)->nullable();
            $table->tinyInteger('is_mobility')->nullable()->default(0);
            $table->string('abroad_school', 191)->nullable();
            $table->foreignId('year_id')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(0);
            $table->date('graduated_at')->nullable();
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
