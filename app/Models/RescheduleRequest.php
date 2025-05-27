<?php

namespace App\Models;

use App\Enums\RescheduleRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'timetable_id',
        'student_id',
        'status',
        'reason',
        'admin_notes',
        'preferred_timeslot_id',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'status' => RescheduleRequestStatus::class,
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function preferredTimeslot(): BelongsTo
    {
        return $this->belongsTo(Timeslot::class, 'preferred_timeslot_id');
    }
}
