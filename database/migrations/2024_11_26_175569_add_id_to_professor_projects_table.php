<?php

use App\Enums\SupervisionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // change table anme from professor_project to professor_projects
        if (Schema::hasTable('professor_project')) {
            Schema::rename('professor_project', 'professor_projects');
        }

        // Drop the existing primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropPrimary(['professor_id', 'project_id']);
        });

        // Drop foreign key constraints with old names
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropForeign('professor_project_professor_id_foreign');
            $table->dropForeign('professor_project_project_id_foreign');
            // drop indexes
            $table->dropUnique('professor_id_project_id');
            // $table->dropIndex('project_id');

        });

        // Add id column and make it primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->id()->first();
            // $table->unique(['professor_id', 'project_id']);
            /* add other pivot
            'supervision_status',
                'last_meeting_date',
                'next_meeting_date', */
            $table->enum('supervision_status', SupervisionStatus::getValues())->nullable()->after('votes');
            $table->date('last_meeting_date')->nullable()->after('supervision_status');
            $table->date('next_meeting_date')->nullable()->after('last_meeting_date');

        });

        // Update existing records with auto-incrementing IDs
        DB::statement('UPDATE professor_projects SET id = (SELECT @row := @row + 1 FROM (SELECT @row := 0) r)');
    }

    public function down()
    {
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['professor_id', 'project_id']);
        });
    }
};
