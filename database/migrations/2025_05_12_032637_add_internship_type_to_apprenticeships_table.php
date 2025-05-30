<?php

use App\Enums\InternshipType;
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
        Schema::table('apprenticeships', function (Blueprint $table) {
            $table->string('internship_type')->nullable()->after('workload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apprenticeships', function (Blueprint $table) {
            $table->dropColumn('internship_type');
        });
    }
};
