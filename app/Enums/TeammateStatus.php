<?php

namespace App\Enums;





enum TeammateStatus: string
{
    case Sent = "Sent";
    case Approved = "Approved";
    case Rejected = "Rejected";
    public static function getArray(): array
    {
        return [
            self::Sent,
            self::Approved,
            self::Rejected,
        ];
    }
    
    public function getLabel(): ?string
    {
        return __($this->name);
    }
    public function getColor(): ?string {
        return match($this) {

        };
    }
}