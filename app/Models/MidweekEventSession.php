<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidweekEventSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_start_at',
        'session_end_at',
        'session_notes',
    ];

    protected $casts = [
        'session_start_at' => 'datetime',
        'session_end_at' => 'datetime',
    ];
}
