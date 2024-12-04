<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('final_year_internship_agreements', function (Blueprint $table) {
            // $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('internships', function (Blueprint $table) {
            // $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }

    public function down()
    {
        Schema::table('final_year_internship_agreements', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained();
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained();
        });

    }
};
