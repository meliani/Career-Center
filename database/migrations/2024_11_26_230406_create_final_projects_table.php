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
        Schema::create('final_projects', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->string('language')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->dateTime('organization_evaluation_received_at')->nullable();
            $table->bigInteger('organization_evaluation_received_by')->nullable();
            $table->enum('defense_status', ['pending', 'authorized', 'rejected'])->default('pending');
            $table->dateTime('defense_authorized')->nullable();
            $table->bigInteger('defense_authorized_by')->nullable();
            $table->text('evaluation_sheet_url')->nullable();
            $table->text('organization_evaluation_sheet_url')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('external_supervisor_id')->nullable()->constrained('final_year_internship_contacts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('final_projects', function (Blueprint $table) {
            $table->index('defense_status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_projects');
    }
};
