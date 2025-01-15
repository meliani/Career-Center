<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->index(['project_id', 'jury_role']);
            $table->index(['professor_id', 'jury_role']);
        });

        Schema::table('final_year_internship_agreements', function (Blueprint $table) {
            $table->index(['project_id', 'assigned_department'])->name('project_id_assigned_department_index');
            $table->index('organization_id');
        });
    }

    public function down()
    {
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'jury_role']);
            $table->dropIndex(['professor_id', 'jury_role']);
        });

        Schema::table('final_year_internship_agreements', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'assigned_department'])->name('project_id_assigned_department_index');
            $table->dropIndex('organization_id');
        });
    }
};
