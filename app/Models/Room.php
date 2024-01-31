<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\baseModel;
use App\Enums\Room as RoomEnum;

class Room extends BaseModel
{

    public static function getInstances(): array
    {
        foreach (RoomEnum::getInstances() as $room) {
            $rooms[] = new self($room);
        }
        return $rooms;
    }

}
