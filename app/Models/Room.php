<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\baseModel;
use App\Enums\Room as RoomEnum;
use App\Enums;
class Room extends BaseModel
{

    // public static function getInstances(): array
    // {
    //     foreach (RoomEnum::getInstances() as $room) {
    //         $rooms[] = new self($room);
    //     }
    //     return $rooms;
    // }
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
