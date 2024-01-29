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
        Schema::create('internship_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            // ->constrained('projects');
            $table->foreignId('internship_id');
            // ->constrained('internships');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_project');
    }
};
