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
        Schema::create('final_project_professor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jury_role', Enums\JuryRole::getValues());
            $table->boolean('is_president')->nullable();
            $table->boolean('was_present')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Add unique constraint to prevent duplicate entries
            $table->unique(['final_project_id', 'professor_id']);

            // Add indexes for better query performance
            $table->index('final_project_id');
            $table->index('professor_id');
            $table->index('jury_role');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_project_professor');
    }
};
