<?php

namespace App\Enums;





enum TeammateStatus: string
{
    case Sent = "Sent";
    case Approved = "Approved";
    case Rejected = "Rejected";

    public function getLabel(): ?string
    {
        return __($this->name);
    }
    public function getColor(): ?string {
        return match($this) {

        };
    }
}