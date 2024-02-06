<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Action: string implements BaseEnum, HasLabel
{
    case PendingAnnouncement = 'Pending Announcement';
    case PendingValidation = 'Pending Validate';
    case PendingApproval = 'Pending Approval';
    case PendingSignOff = 'Pending Sign Off';
    case PendingStart = 'Pending Start';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
