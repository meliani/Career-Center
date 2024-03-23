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
    use \Spatie\Tags\HasTags;

    protected static function booted(): void
    {

        static::creating(function ($ticket) {
            $ticket->user_id = auth()->id();
            $ticket->status = Enums\TicketStatus::Open;
        });
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'closed_reason',
        'assigned_to',
        'closed_at',
        'response',
    ];

    protected $casts = [
        'status' => Enums\TicketStatus::class,
        'closed_reason' => Enums\TicketClosedReason::class,
    ];

    public function closeTicket()
    {
        $this->status = 'Closed';
        $this->closed_at = now();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to')->where('role', 'SuperAdministrator');
    }
}
