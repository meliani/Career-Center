<?php

use App\Enums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('response')->nullable();
            $table->enum('status', Enums\TicketStatus::getValues())->default(Enums\TicketStatus::Open->value)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable()->constrained('users');
            $table->enum('closed_reason', Enums\TicketClosedReason::getValues())->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
