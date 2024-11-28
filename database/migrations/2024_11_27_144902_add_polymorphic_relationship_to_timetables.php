<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // Add new columns
        Schema::table('timetables', function (Blueprint $table) {
            $table->string('schedulable_type')->nullable();
            $table->unsignedBigInteger('schedulable_id')->nullable();
        });

        // Update existing project timetables
        DB::table('timetables')
            ->whereNotNull('project_id')
            ->update([
                'schedulable_type' => 'App\Models\Project',
                'schedulable_id' => DB::raw('project_id'),
            ]);
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
