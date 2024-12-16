<?php

use App\Enums\CollaborationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaboration_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('students');
            $table->foreignId('receiver_id')->constrained('students');
            $table->foreignId('year_id')->constrained('years');
            $table->text('message');
            $table->enum('status', CollaborationStatus::getValues())->default(CollaborationStatus::Pending);
            $table->timestamps();

            // Prevent multiple requests between the same students in the same year
            $table->unique(['sender_id', 'receiver_id', 'year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaboration_requests');
    }
};
