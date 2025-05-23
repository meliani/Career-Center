<?php

namespace App\Filament\Actions\Action\Processing;

// use App\Services\TimeslotService;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Artisan;

class GenerateTimeslotsFromArtisanAction extends Action
{
    public $scheduleParameters;

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()
            ->action(function ($record): void {
                $currentYear = \App\Models\Year::current();
                
                Artisan::call('app:generate-timeslots', [
                    '--start-date' => $record->schedule_starting_at,
                    '--end-date' => $record->schedule_ending_at,
                    '--year-id' => $currentYear->id,
                ]);
            });

        return $static;
    }
}
