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
        Schema::table('apprenticeships', function (Blueprint $table) {
            $table->string('verification_string')->nullable()->after('verification_document_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apprenticeships', function (Blueprint $table) {
            $table->dropColumn('verification_string');
        });
    }
};
