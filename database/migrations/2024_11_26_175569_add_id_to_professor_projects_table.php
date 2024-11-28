<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // change table anme from professor_project to professor_projects
        Schema::rename('professor_project', 'professor_projects');

        // Drop the existing primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropPrimary(['professor_id', 'project_id']);
        });

        // Add id column and make it primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->id()->first();
            $table->unique(['professor_id', 'project_id']);
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
