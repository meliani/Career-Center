<?php

namespace App\Filament\Actions\Action\Processing;

use App\Services;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class GenerateTimetableFromArtisanAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function ($record): void {

            $startDate = $record->schedule_starting_at;
            $endDate = $record->schedule_ending_at;
            $force = false;
            $user = 1;

            Artisan::call('app:generate-planning', [
                '--force' => $force,
                '--user' => $user,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $numberOfAssignedProjects = Services\TimetableService::generateTimetable();

            Notification::make()
                ->title($numberOfAssignedProjects . ' Projects Timetable has been generated successfully')
                ->success()
                ->send();
        });

        return $static;
    }
}
