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
        Schema::table('students', function (Blueprint $table) {
            // Personal information
            $table->string('matricule', 20)->nullable()->after('id_pfe');
            $table->string('birth_place', 191)->nullable()->after('birth_date');
            $table->string('birth_place_ar', 191)->nullable()->after('birth_place');
            $table->string('nationality', 50)->nullable()->after('birth_place_ar');
            $table->string('address', 255)->nullable()->after('nationality');
            $table->string('city', 100)->nullable()->after('address');
            
            // Baccalaureate information
            $table->string('bac_year', 10)->nullable()->after('city');
            $table->string('bac_type', 100)->nullable()->after('bac_year');
            $table->string('bac_mention', 50)->nullable()->after('bac_type');
            $table->string('bac_place', 100)->nullable()->after('bac_mention');
            
            // CNC information
            $table->string('cnc', 20)->nullable()->after('bac_place');
            $table->string('cnc_filiere', 100)->nullable()->after('cnc');
            $table->string('cnc_rank', 20)->nullable()->after('cnc_filiere');
            
            // Contact information
            $table->string('father_phone', 50)->nullable()->after('cnc_rank');
            $table->string('mother_phone', 50)->nullable()->after('father_phone');
            
            // Enrollment information
            $table->string('enrollment_year', 20)->nullable()->after('mother_phone');
            $table->string('access_path', 50)->nullable()->after('enrollment_year');
            $table->string('enrollment_status', 50)->nullable()->after('access_path');
            
            // ID information
            $table->string('cine', 50)->nullable()->after('enrollment_status');
            $table->string('passport', 50)->nullable()->after('cine');
            $table->string('massar_code', 50)->nullable()->after('passport');
            
            // Arabic names
            $table->string('first_name_ar', 191)->nullable()->after('massar_code');
            $table->string('last_name_ar', 191)->nullable()->after('first_name_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'matricule',
                'birth_place',
                'birth_place_ar',
                'nationality',
                'address',
                'city',
                'bac_year',
                'bac_type',
                'bac_mention',
                'bac_place',
                'cnc',
                'cnc_filiere',
                'cnc_rank',
                'father_phone',
                'mother_phone',
                'enrollment_year',
                'access_path',
                'enrollment_status',
                'cine',
                'passport',
                'massar_code',
                'first_name_ar',
                'last_name_ar'
            ]);
        });
    }
};
