<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        // Migrate existing relationships
        DB::statement('
            INSERT INTO project_agreements (project_id, agreeable_id, agreeable_type)
            SELECT project_id, id, "App\\\\Models\\\\InternshipAgreement"
            FROM internships
            WHERE project_id IS NOT NULL
        ');

        DB::statement('
            INSERT INTO project_agreements (project_id, agreeable_id, agreeable_type)
            SELECT project_id, id, "App\\\\Models\\\\FinalYearInternshipAgreement"
            FROM final_year_internship_agreements
            WHERE project_id IS NOT NULL
        ');
    }

    public function down()
    {

        // Restore old relationships
        DB::statement('
            UPDATE internships i
            INNER JOIN project_agreements pa ON
                pa.agreeable_id = i.id AND
                pa.agreeable_type = "App\\\\Models\\\\InternshipAgreement"
            SET i.project_id = pa.project_id
        ');

        DB::statement('
            UPDATE final_year_internship_agreements f
            INNER JOIN project_agreements pa ON
                pa.agreeable_id = f.id AND
                pa.agreeable_type = "App\\\\Models\\\\FinalYearInternshipAgreement"
            SET f.project_id = pa.project_id
        ');

        Schema::dropIfExists('project_agreements');
    }
};
