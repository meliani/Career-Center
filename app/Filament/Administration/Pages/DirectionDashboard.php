<?php

namespace App\Filament\Administration\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class DirectionDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'directionDashboard';

    protected static ?string $title = 'Direction dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Direction dashboard';

    use HasFiltersAction;

    // protected int|string|array $columnSpan = 'full';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isDirection();
        } else {
            return false;
        }
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            // FilterAction::make()
            //     ->form([
            //         DatePicker::make('startDate'),
            //         DatePicker::make('endDate'),
            //         // ...
            //     ]),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel);
    }
}
