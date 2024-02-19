<?php

namespace App\Filament\Actions;

use App\Services;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;

class GenerateTimetableAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (): void {

            $numberOfAssignedProjects = Services\TimetableService::generateTimetable();

            Notification::make()
                ->title($numberOfAssignedProjects.' Projects Timetable has been generated successfully')
                ->success()
                ->send();
        });

        return $static;
    }
}
