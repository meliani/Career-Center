<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        // Rename the table
        Schema::rename('internships', 'internship_agreements');

        // Add new columns to the table
        Schema::table('internship_agreements', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->after('year_id');
            $table->unsignedBigInteger('parrain_id')->nullable();
            $table->unsignedBigInteger('external_supervisor_id')->nullable();
            $table->unsignedBigInteger('internal_supervisor_id')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_file_name')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('is_signed_by_student')->nullable();
            $table->boolean('is_signed_by_organization')->nullable();
            $table->boolean('is_signed_by_administration')->nullable();
            $table->dateTime('signed_by_student_at')->nullable();
            $table->dateTime('signed_by_organization_at')->nullable();
            $table->dateTime('signed_by_administration_at')->nullable();
            $table->string('verification_document_url')->nullable();

            // Modify existing columns to match types
            $table->text('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->decimal('remuneration', 8, 2)->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->integer('workload')->nullable();
        });

        // Migrate data to new tables before dropping old columns

        // Map of country names to country codes
        $countryCodes = [
            'Morocco' => 'MA',
            'France' => 'FR',
            'Canada' => 'CA',
            // Add other countries as needed
        ];

        // Get all internship agreements
        $agreements = DB::table('internship_agreements')->get();

        foreach ($agreements as $agreement) {
            // Convert country name to country code
            $countryCode = $countryCodes[$agreement->country] ?? null;

            // Create or find organization
            DB::table('organizations')->updateOrInsert(
                ['slug' => Str::slug($agreement->central_organization)],
                [
                    'name' => $agreement->central_organization,
                    'slug' => Str::slug($agreement->central_organization),
                    'address' => $agreement->adresse,
                    'city' => $agreement->city,
                    'country' => $countryCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $organization = DB::table('organizations')
                ->where('slug', Str::slug($agreement->central_organization))
                ->first();

            // Create parrain contact
            $parrainId = DB::table('internship_agreement_contacts')->insertGetId([
                'organization_id' => $organization->id,
                'role' => 'Parrain',
                'title' => $agreement->parrain_titre,
                'first_name' => $agreement->parrain_prenom,
                'last_name' => $agreement->parrain_nom,
                'function' => $agreement->parrain_fonction,
                'phone' => $agreement->parrain_tel,
                'email' => $agreement->parrain_mail,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create external supervisor contact
            $supervisorId = DB::table('internship_agreement_contacts')->insertGetId([
                'organization_id' => $organization->id,
                'role' => 'Supervisor',
                'title' => $agreement->encadrant_ext_titre,
                'first_name' => $agreement->encadrant_ext_prenom,
                'last_name' => $agreement->encadrant_ext_nom,
                'function' => $agreement->encadrant_ext_fonction,
                'phone' => $agreement->encadrant_ext_tel,
                'email' => $agreement->encadrant_ext_mail,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update agreement with organization_id and contact IDs
            DB::table('internship_agreements')
                ->where('id', $agreement->id)
                ->update([
                    'organization_id' => $organization->id,
                    'parrain_id' => $parrainId,
                    'external_supervisor_id' => $supervisorId,
                ]);

            // Copy 'id_pfe' from 'internship_agreements' to 'students'
            DB::table('students')
                ->where('id', $agreement->student_id)
                ->update(['id_pfe' => $agreement->id_pfe]);
        }

        // Now remove the old columns
        Schema::table('internship_agreements', function (Blueprint $table) {
            // Remove organization-related columns
            $table->dropColumn([
                'organization_name',
                'central_organization',
                'adresse',
                'city',
                'country',
            ]);

            // Remove parrain and external supervisor columns
            $table->dropColumn([
                'parrain_titre',
                'parrain_nom',
                'parrain_prenom',
                'parrain_fonction',
                'parrain_tel',
                'parrain_mail',
                'encadrant_ext_titre',
                'encadrant_ext_nom',
                'encadrant_ext_prenom',
                'encadrant_ext_fonction',
                'encadrant_ext_tel',
                'encadrant_ext_mail',
            ]);

            // Remove unused columns
            $table->dropColumn([
                'keywords',
                'load',
                'int_adviser_name',
                'id_pfe',
            ]);

            // Add foreign key constraint
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
        });
    }

    public function down()
    {
        // Reverse the operations
        Schema::table('internship_agreements', function (Blueprint $table) {
            // Add back the removed columns
            $table->string('organization_name', 191)->after('id_pfe');
            $table->string('adresse', 255);
            $table->string('city', 191);
            $table->string('country', 191);

            $table->string('parrain_titre', 191);
            $table->string('parrain_nom', 191);
            $table->string('parrain_prenom', 191);
            $table->string('parrain_fonction', 191);
            $table->string('parrain_tel', 191);
            $table->string('parrain_mail', 191);

            $table->string('encadrant_ext_titre', 191);
            $table->string('encadrant_ext_nom', 191);
            $table->string('encadrant_ext_prenom', 191);
            $table->string('encadrant_ext_fonction', 191);
            $table->string('encadrant_ext_tel', 191);
            $table->string('encadrant_ext_mail', 191);

            $table->text('keywords');
            $table->string('load', 191)->nullable();
            $table->string('int_adviser_name', 191)->nullable();
            $table->unsignedInteger('id_pfe')->nullable();
            $table->unsignedInteger('project_id')->nullable();

            // Remove added columns
            $table->dropColumn([
                'organization_id',
                'parrain_id',
                'external_supervisor_id',
                'internal_supervisor_id',
                'pdf_path',
                'pdf_file_name',
                'cancelled_at',
                'cancellation_reason',
                'is_signed_by_student',
                'is_signed_by_organization',
                'is_signed_by_administration',
                'signed_by_student_at',
                'signed_by_organization_at',
                'signed_by_administration_at',
                'verification_document_url',
                'workload',
            ]);

        });

        // Copy 'id_pfe' back from 'students' to 'internship_agreements'
        $students = DB::table('students')->get();

        foreach ($students as $student) {
            DB::table('internship_agreements')
                ->where('student_id', $student->id)
                ->update(['id_pfe' => $student->id_pfe]);
        }

        // Remove 'id_pfe' from 'students' table
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('id_pfe');
        });

        // Rename the table back
        Schema::rename('internship_agreements', 'internships');
    }
};
