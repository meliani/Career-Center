<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {

        static::creating(function ($ticket) {
            $ticket->user_id = auth()->id();
            $ticket->status = Enums\TicketStatus::Open;
        });
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        // 'user_id',
    ];

    protected $casts = [
        'status' => Enums\TicketStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to')->where('role', 'SuperAdministrator');
    }
}
