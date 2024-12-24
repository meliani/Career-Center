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
        Schema::create('master_research_internship_agreements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id');
            $table->unsignedInteger('year_id');
            $table->unsignedInteger('final_project_id')->nullable();
            $table->string('status', 255)->nullable();
            $table->dateTime('announced_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->enum('assigned_department', Enums\Department::getValues())->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('signed_at')->nullable();
            $table->text('observations')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->string('office_location')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('starting_at')->nullable();
            $table->dateTime('ending_at')->nullable();
            $table->decimal('remuneration', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->integer('workload')->nullable();
            $table->bigInteger('parrain_id')->nullable();
            $table->bigInteger('external_supervisor_id')->nullable();
            $table->bigInteger('internal_supervisor_id')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_file_name')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('signed_by_student_at')->nullable();
            $table->dateTime('signed_by_organization_at')->nullable();
            $table->dateTime('signed_by_administration_at')->nullable();
            $table->string('verification_document_url')->nullable();
            $table->string('verification_string')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_research_internship_agreements');
    }
};
