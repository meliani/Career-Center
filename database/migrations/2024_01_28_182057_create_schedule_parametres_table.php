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
        Schema::create('schedule_parameters', function (Blueprint $table) {
            $table->id();
            $table->date('starting_from');
            $table->date('ending_at');
            $table->time('working_from');
            $table->time('working_to');
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
