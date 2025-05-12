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
        Schema::create('apprenticeship_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprenticeship_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->date('new_starting_at')->nullable();
            $table->date('new_ending_at')->nullable();
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->longText('reason')->nullable(); // Reason for the amendment
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->longText('validation_comment')->nullable(); // Admin comment on validation/rejection
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apprenticeship_amendments');
    }
};
