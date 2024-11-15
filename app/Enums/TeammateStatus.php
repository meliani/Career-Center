<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;

enum TeammateStatus: string
{
    use HasBaseEnumFeatures;

    case Sent = "Sent";
    case Approved = "Approved";
    case Rejected = "Rejected";

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): ?string
    {
        return match($this) {

        };
    }
}
