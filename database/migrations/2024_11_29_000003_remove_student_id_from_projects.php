<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Remove student_id from projects table if it exists
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'student_id')) {
                //     $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });

        // Remove student_id from final_projects table if it exists
        Schema::table('final_projects', function (Blueprint $table) {
            if (Schema::hasColumn('final_projects', 'student_id')) {
                // $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('final_projects', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
