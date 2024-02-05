<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleParametresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_parameters', function (Blueprint $table) {
            $table->id();
            $table->dateTime('schedule_starting_at');
            $table->dateTime('schedule_ending_at');
            $table->time('day_starting_at');
            $table->time('day_ending_at');
            $table->time('lunch_starting_at');
            $table->time('lunch_ending_at');
            $table->integer('number_of_rooms');
            $table->integer('max_defenses_per_professor');
            $table->integer('max_rooms');
            $table->integer('minutes_per_slot');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_parameters');
    }
};
