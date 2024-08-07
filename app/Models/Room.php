<?php

namespace App\Models;

use App\Enums;

class Room extends Core\BackendBaseModel
{
    // public static function getInstances(): array
    // {
    //     foreach (RoomEnum::getInstances() as $room) {
    //         $rooms[] = new self($room);
    //     }
    //     return $rooms;
    // }

    public function scopeAvailable($query)
    {
        return $query->where('status', Enums\RoomStatus::Available);
    }

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => Enums\RoomStatus::class,
    ];
}
