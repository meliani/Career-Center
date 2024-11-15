<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use App\Enums\Concerns\HasBaseEnumFeatures;

enum Action: string implements HasLabel
{
    use HasBaseEnumFeatures;

    case PendingAnnouncement = 'Pending Announcement';
    case PendingValidation = 'Pending Validate';
    case PendingApproval = 'Pending Approval';
    case PendingSignOff = 'Pending Sign Off';
    case PendingStart = 'Pending Start';

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    // Implement getColor() and getIcon() methods if needed
    public function getColor(): ?string
    {
        // Define colors if applicable
        return null;
    }

    public function getIcon(): ?string
    {
        // Define icons if applicable
        return null;
    }
}
