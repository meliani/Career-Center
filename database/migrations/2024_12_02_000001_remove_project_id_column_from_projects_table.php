<?php

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
        // Only drop columns if they exist
        if (Schema::hasTable('final_year_internship_agreements') && 
            Schema::hasColumn('final_year_internship_agreements', 'project_id')) {
            Schema::table('final_year_internship_agreements', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }

        if (Schema::hasTable('internships') && 
            Schema::hasColumn('internships', 'project_id')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the columns back if the tables exist
        if (Schema::hasTable('final_year_internship_agreements')) {
            Schema::table('final_year_internship_agreements', function (Blueprint $table) {
                if (!Schema::hasColumn('final_year_internship_agreements', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('internships')) {
            Schema::table('internships', function (Blueprint $table) {
                if (!Schema::hasColumn('internships', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                }
            });
        }
    }
};
