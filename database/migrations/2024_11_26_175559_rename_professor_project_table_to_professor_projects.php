<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // change table anme from professor_project to professor_projects
        if (Schema::hasTable('professor_project')) {
            Schema::rename('professor_project', 'professor_projects');
        }

    }

    public function down()
    {
        Schema::rename('professor_projects', 'professor_project');
    }
};
