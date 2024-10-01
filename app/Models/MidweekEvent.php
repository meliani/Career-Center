<?php

namespace App\Models;

use App\Enums\EventParticipationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MidweekEvent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'participation_status',
        'organization_account_id',
        'meeting_confirmed_by',
        'meeting_confirmed_at',
        'room_id',
        'midweek_event_session_id',
        'notes',
    ];

    protected $casts = [
        'meeting_confirmed_at' => 'datetime',
        'participation_status' => EventParticipationStatus::class,

    ];

    public function organizationAccount()
    {
        return $this->belongsTo(OrganizationAccount::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function midweekEventSession()
    {
        return $this->belongsTo(MidweekEventSession::class);
    }

    public function meetingConfirmedBy()
    {
        return $this->belongsTo(User::class, 'meeting_confirmed_by')
            ->where('role', \App\Enums\Role::Administrator);
    }
}
