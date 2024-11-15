<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TimelineCategory: string implements HasColor, HasLabel
{
    use HasBaseEnumFeatures;

    case Administrative = 'administrative';
    case Event = 'event';
    case Deadline = 'deadline';
    case Other = 'other';
    case Academic = 'academic';
    case Communication = 'communication';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Administrative => __('Administrative'),
            self::Event => __('Event'),
            self::Deadline => __('Deadline'),
            self::Other => __('Other'),
            self::Academic => __('Academic'),
            self::Communication => __('Communication'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Administrative => '#64748b', // slate-500
            self::Event => '#0ea5e9', // sky-500
            self::Deadline => '#ef4444', // red-500
            self::Other => '#22c55e', // green-500
            self::Academic => '#8b5cf6', // violet-500
            self::Communication => '#f59e0b', // amber-500
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Administrative => 'heroicon-o-document-text',
            self::Event => 'heroicon-o-calendar',
            self::Deadline => 'heroicon-o-clock',
            self::Other => 'heroicon-o-clipboard-document-check',
            self::Academic => 'heroicon-o-academic-cap',
            self::Communication => 'heroicon-o-chat-bubble-left-right',
        };
    }
}
