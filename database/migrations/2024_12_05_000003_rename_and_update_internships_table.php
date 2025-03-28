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
        // Rename the table first if it hasn't been already
        if (Schema::hasTable('internships') && !Schema::hasTable('internship_agreements')) {
            Schema::rename('internships', 'internship_agreements');
        }

        if (Schema::hasTable('internship_agreements')) {
            Schema::table('internship_agreements', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('year_id');
                $table->unsignedBigInteger('parrain_id')->nullable();
                $table->unsignedBigInteger('external_supervisor_id')->nullable();
                $table->unsignedBigInteger('internal_supervisor_id')->nullable();
                $table->string('pdf_path')->nullable();
                $table->string('pdf_file_name')->nullable();
                $table->dateTime('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
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
        }

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
        if (Schema::hasTable('internship_agreements')) {
            Schema::table('internship_agreements', function (Blueprint $table) {
                // Only drop columns if they exist
                $columnsToCheck = [
                    'organization_name',
                    'central_organization',
                    'adresse',
                    'city',
                    'country',
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
                    'keywords',
                    'load',
                    'int_adviser_name',
                    'id_pfe',
                ];

                $columnsToRemove = [];
                foreach ($columnsToCheck as $column) {
                    if (Schema::hasColumn('internship_agreements', $column)) {
                        $columnsToRemove[] = $column;
                    }
                }

                if (!empty($columnsToRemove)) {
                    $table->dropColumn($columnsToRemove);
                }

                // Add foreign key constraint
                if (!Schema::hasColumn('internship_agreements', 'organization_id')) {
                    $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('internship_agreements')) {
            Schema::table('internship_agreements', function (Blueprint $table) {
                // Add back the removed columns
                if (!Schema::hasColumn('internship_agreements', 'organization_name')) {
                    $table->string('organization_name', 191)->after('id_pfe');
                }
                if (!Schema::hasColumn('internship_agreements', 'adresse')) {
                    $table->string('adresse', 255);
                }
                if (!Schema::hasColumn('internship_agreements', 'city')) {
                    $table->string('city', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'country')) {
                    $table->string('country', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_titre')) {
                    $table->string('parrain_titre', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_nom')) {
                    $table->string('parrain_nom', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_prenom')) {
                    $table->string('parrain_prenom', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_fonction')) {
                    $table->string('parrain_fonction', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_tel')) {
                    $table->string('parrain_tel', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'parrain_mail')) {
                    $table->string('parrain_mail', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_titre')) {
                    $table->string('encadrant_ext_titre', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_nom')) {
                    $table->string('encadrant_ext_nom', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_prenom')) {
                    $table->string('encadrant_ext_prenom', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_fonction')) {
                    $table->string('encadrant_ext_fonction', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_tel')) {
                    $table->string('encadrant_ext_tel', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'encadrant_ext_mail')) {
                    $table->string('encadrant_ext_mail', 191);
                }
                if (!Schema::hasColumn('internship_agreements', 'keywords')) {
                    $table->text('keywords');
                }
                if (!Schema::hasColumn('internship_agreements', 'load')) {
                    $table->string('load', 191)->nullable();
                }
                if (!Schema::hasColumn('internship_agreements', 'int_adviser_name')) {
                    $table->string('int_adviser_name', 191)->nullable();
                }
                if (!Schema::hasColumn('internship_agreements', 'id_pfe')) {
                    $table->unsignedInteger('id_pfe')->nullable();
                }
                if (!Schema::hasColumn('internship_agreements', 'project_id')) {
                    $table->unsignedInteger('project_id')->nullable();
                }

                // Remove added columns
                if (Schema::hasColumn('internship_agreements', 'organization_id')) {
                    $table->dropConstrainedForeignId('organization_id');
                }
                if (Schema::hasColumn('internship_agreements', 'parrain_id')) {
                    $table->dropColumn('parrain_id');
                }
                if (Schema::hasColumn('internship_agreements', 'external_supervisor_id')) {
                    $table->dropColumn('external_supervisor_id');
                }
                if (Schema::hasColumn('internship_agreements', 'internal_supervisor_id')) {
                    $table->dropColumn('internal_supervisor_id');
                }
                if (Schema::hasColumn('internship_agreements', 'pdf_path')) {
                    $table->dropColumn('pdf_path');
                }
                if (Schema::hasColumn('internship_agreements', 'pdf_file_name')) {
                    $table->dropColumn('pdf_file_name');
                }
                if (Schema::hasColumn('internship_agreements', 'cancelled_at')) {
                    $table->dropColumn('cancelled_at');
                }
                if (Schema::hasColumn('internship_agreements', 'cancellation_reason')) {
                    $table->dropColumn('cancellation_reason');
                }
                if (Schema::hasColumn('internship_agreements', 'signed_by_student_at')) {
                    $table->dropColumn('signed_by_student_at');
                }
                if (Schema::hasColumn('internship_agreements', 'signed_by_organization_at')) {
                    $table->dropColumn('signed_by_organization_at');
                }
                if (Schema::hasColumn('internship_agreements', 'signed_by_administration_at')) {
                    $table->dropColumn('signed_by_administration_at');
                }
                if (Schema::hasColumn('internship_agreements', 'verification_document_url')) {
                    $table->dropColumn('verification_document_url');
                }
                if (Schema::hasColumn('internship_agreements', 'workload')) {
                    $table->dropColumn('workload');
                }
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

            // Rename the table back only if renaming makes sense in your context
            if (!Schema::hasTable('internships')) {
                Schema::rename('internship_agreements', 'internships');
            }
        }
    }
};
