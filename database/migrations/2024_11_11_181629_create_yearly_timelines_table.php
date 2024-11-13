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
        Schema::create('yearly_timelines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->string('category')->nullable();
            $table->integer('priority')->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('year_id')->constrained('years')->cascadeOnDelete();
            $table->boolean('is_highlight')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yearly_timelines');
    }
};
