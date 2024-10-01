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
        Schema::create('midweek_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('participation_status')->nullable();
            $table->foreignId('organization_account_id')->constrained();
            $table->string('meeting_confirmed_by')->nullable();
            $table->dateTime('meeting_confirmed_at')->nullable();
            $table->foreignId('room_id')->nullable()->constrained();
            $table->foreignId('midweek_event_session_id')->nullable()->constrained();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midweek_events');
    }
};
