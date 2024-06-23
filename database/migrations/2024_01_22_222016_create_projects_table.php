<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasTable('projects')) {
            return;
        }
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->dateTime('agreement_verified')->nullable();
            $table->bigInteger('agreement_verified_by')->nullable();
            $table->dateTime('supervisor_approved')->nullable();
            $table->bigInteger('supervisor_approved_by')->nullable();
            $table->dateTime('organization_evaluation_received')->nullable();
            $table->bigInteger('organization_evaluation_received_by')->nullable();
            $table->enum('defense_status', ['pending', 'authorized', 'rejected'])->default('pending');
            $table->dateTime('defense_authorized')->nullable();
            $table->bigInteger('defense_authorized_by')->nullable();
            $table->text('evaluation_sheet_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
}
