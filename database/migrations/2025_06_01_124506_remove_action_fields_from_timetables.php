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
        Schema::table('timetables', function (Blueprint $table) {
            // First drop foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['confirmed_by']);
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['rescheduled_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Then drop the columns
            $table->dropColumn([
                'user_id',
                'confirmed_at',
                'cancelled_at',
                'rescheduled_at',
                'confirmed_by',
                'cancelled_by',
                'rescheduled_by',
                'created_by',
                'updated_by'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Recreate columns
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->unsignedBigInteger('rescheduled_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Recreate foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rescheduled_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
