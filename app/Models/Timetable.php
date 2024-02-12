<?php

namespace App\Models;

use App\Enums\Room as RoomEnum;

class Timetable extends Core\BackendBaseModel
{
    /* Database Fields 
            Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('timeslot_id')->constrained();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_taken')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_rescheduled')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->foreignId('rescheduled_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            // timeslot_id and date should be unique
            $table->unique(['timeslot_id', 'date', 'room_id', 'project_id']);
    
    */
}
