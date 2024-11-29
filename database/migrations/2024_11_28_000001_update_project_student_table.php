<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('project_student', function (Blueprint $table) {
            // First remove existing foreign key constraints
            $table->dropForeign(['project_id']);

            // Add new columns for polymorphic relationship
            $table->string('project_type')->after('id');

            // Rename project_id to maintain consistent naming
            $table->renameColumn('project_id', 'project_id');

            // Create a new composite index for polymorphic relationship
            $table->index(['project_type', 'project_id'], 'project_student_project_index');

            // Add status and metadata columns if needed
            $table->string('status')->nullable()->after('project_id');
            $table->json('metadata')->nullable()->after('status');
            // $table->timestamps();
        });

        // Update existing records to set project_type
        DB::table('project_student')->update([
            'project_type' => 'App\\Models\\Project',
        ]);
    }

    public function down()
    {
        Schema::table('project_student', function (Blueprint $table) {
            $table->dropIndex('project_student_project_index');
            $table->dropColumn('project_type');

            // Restore foreign key if needed
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->dropColumn(['status', 'metadata']);
        });
    }
};
