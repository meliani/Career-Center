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
        Schema::create('midweek_event_sessions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('session_start_at');
            $table->dateTime('session_end_at');
            $table->text('session_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midweek_event_sessions');
    }
};
