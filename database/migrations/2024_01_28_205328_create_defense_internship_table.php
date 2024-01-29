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
        Schema::create('defense_internship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_id')->constrained('defenses');
            $table->foreignId('internship_id');
            // ->constrained('internships');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defense_internship');
    }
};
