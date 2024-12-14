<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollaborationRequest extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'status',
        'year_id',
    ];

    protected $casts = [
        'status' => \App\Enums\CollaborationStatus::class,
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'receiver_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }
}
