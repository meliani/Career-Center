<?php

use App\Enums\AlumniDegree;
use App\Enums\Program;
use App\Enums\WorkStatus;
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
        Schema::create('alumni_references', function (Blueprint $table) {
            $table->id();
            $table->enum('title', ['Mr', 'Mrs'])->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();

            $table->string('phone_number')->nullable();
            $table->foreignId('graduation_year_id')->nullable();
            $table->enum('degree', array_map(fn ($case) => $case->name, AlumniDegree::cases()))->nullable();
            $table->enum('assigned_program', array_map(fn ($case) => $case->name, Program::cases()))->nullable();
            $table->integer('is_enabled')->nullable();
            $table->tinyInteger('is_mobility')->nullable();
            $table->string('abroad_school', 191)->nullable();
            $table->enum('work_status', array_map(fn ($case) => $case->name, WorkStatus::cases()))->nullable();
            $table->string('resume_url')->nullable();

            $table->string('avatar_url')->nullable();
            $table->integer('number_of_bounces')->nullable();
            $table->string('bounce_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_references');
    }
};
