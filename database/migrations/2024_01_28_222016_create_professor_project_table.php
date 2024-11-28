<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorProjectTable extends Migration
{
    public function up(): void
    {
        Schema::create('professor_projects', function (Blueprint $table) {
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->enum('jury_role', Enums\JuryRole::getArray());
            $table->boolean('is_president')->nullable();
            $table->integer('votes')->nullable();
            $table->boolean('was_present')->nullable();
            $table->string('supervision_status')->nullable();
            $table->date('last_meeting_date')->nullable();
            $table->date('next_meeting_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['professor_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professor_projects');
    }
}
