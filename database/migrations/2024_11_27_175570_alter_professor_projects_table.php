<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->string('project_type')->nullable()->after('professor_id');
            // Removed adding 'project_id' since it already exists
        });

        DB::statement("
            UPDATE `professor_projects`
            SET
                `project_type` = 'App\\\\Models\\\\Project'
            WHERE `project_id` IS NOT NULL
        ");

        Schema::table('professor_projects', function (Blueprint $table) {
            $table->unique(
                ['professor_id', 'project_type', 'project_id'],
                'prof_morph_unique'
            );

            $table->index(
                ['project_type', 'project_id'],
                'prof_morph_idx'
            );

            // Removed dropping 'professor_projectable_type' and 'professor_projectable_id' since they do not exist
        });
    }

    public function down(): void
    {
        Schema::table('professor_projects', function (Blueprint $table) {
            // No need to add 'professor_projectable_type' and 'professor_projectable_id' since they did not exist
            $table->dropIndex('prof_morph_idx');
            $table->dropUnique('prof_morph_unique');
            $table->dropColumn('project_type');
            // No need to drop 'project_id' since it was not added in 'up'
        });
    }
};
