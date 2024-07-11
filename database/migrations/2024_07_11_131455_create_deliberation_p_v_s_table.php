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
        Schema::create('deliberation_p_v_s', function (Blueprint $table) {
            $table->id();
            $table->date('meeting_date');
            $table->json('attendees');
            $table->text('decisions');
            $table->text('remarks')->nullable();
            $table->foreignId('year_id')->constrained();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliberation_p_v_s');
    }
};
