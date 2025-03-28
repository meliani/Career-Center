<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->morphs('agreeable');
            $table->timestamps();

            $table->unique(['project_id', 'agreeable_id', 'agreeable_type']);
        });

        // Migrate existing relationships from internships table if project_id column exists
        if (Schema::hasTable('internships') && Schema::hasColumn('internships', 'project_id')) {
            DB::statement('
                INSERT INTO project_agreements (project_id, agreeable_id, agreeable_type, created_at, updated_at)
                SELECT project_id, id, "App\\\\Models\\\\InternshipAgreement", NOW(), NOW()
                FROM internships
                WHERE project_id IS NOT NULL
            ');
        }

        // Only run this part if the final_year_internship_agreements table exists and has a project_id column
        if (Schema::hasTable('final_year_internship_agreements') && Schema::hasColumn('final_year_internship_agreements', 'project_id')) {
            DB::statement('
                INSERT INTO project_agreements (project_id, agreeable_id, agreeable_type, created_at, updated_at)
                SELECT project_id, id, "App\\\\Models\\\\FinalYearInternshipAgreement", NOW(), NOW()
                FROM final_year_internship_agreements
                WHERE project_id IS NOT NULL
            ');
        }
    }

    public function down()
    {
        // Restore old relationships only if the tables and columns exist
        if (Schema::hasTable('internships') && Schema::hasColumn('internships', 'project_id')) {
            DB::statement('
                UPDATE internships i
                INNER JOIN project_agreements pa ON
                    pa.agreeable_id = i.id AND
                    pa.agreeable_type = "App\\\\Models\\\\InternshipAgreement"
                SET i.project_id = pa.project_id
            ');
        }

        if (Schema::hasTable('final_year_internship_agreements') && Schema::hasColumn('final_year_internship_agreements', 'project_id')) {
            DB::statement('
                UPDATE final_year_internship_agreements f
                INNER JOIN project_agreements pa ON
                    pa.agreeable_id = f.id AND
                    pa.agreeable_type = "App\\\\Models\\\\FinalYearInternshipAgreement"
                SET f.project_id = pa.project_id
            ');
        }

        Schema::dropIfExists('project_agreements');
    }
};
