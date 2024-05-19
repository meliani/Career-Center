<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\ScheduleParameters;
use App\Models\Timeslot;
use App\Services\AutoScheduleService;
// use App\Services\TimeslotService;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
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
            Artisan::call('app:generate-timeslots', [
                //     protected $signature = 'app:generate-timeslots {--start-date=2024-06-24} {--end-date=2024-07-24}';
                '--start-date' => $record->start_date,
                '--end-date' => $record->end_date,
            ]);
        });

        return $static;
    }


}
