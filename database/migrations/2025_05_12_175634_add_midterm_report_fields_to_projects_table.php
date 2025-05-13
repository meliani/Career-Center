<?php

use App\Enums\MidTermReportStatus;
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
        Schema::table('projects', function (Blueprint $table) {
            $table->date('midterm_due_date')->nullable();
            $table->string('midterm_report_status')->default(MidTermReportStatus::Pending->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('midterm_due_date');
            $table->dropColumn('midterm_report_status');
        });
    }
};
