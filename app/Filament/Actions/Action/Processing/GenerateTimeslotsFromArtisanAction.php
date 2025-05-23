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
                
                // Validate that we have all required parameters
                if (!$record->schedule_starting_at || !$record->schedule_ending_at) {
                    \Filament\Notifications\Notification::make()
                        ->title('Missing schedule parameters')
                        ->body('Start and end dates are required to generate timeslots.')
                        ->danger()
                        ->send();
                    return;
                }
                
                if (!$record->day_starting_at || !$record->day_ending_at) {
                    \Filament\Notifications\Notification::make()
                        ->title('Missing schedule parameters')
                        ->body('Day start and end times are required to generate timeslots.')
                        ->danger()
                        ->send();
                    return;
                }
                
                if (!$record->minutes_per_slot) {
                    \Filament\Notifications\Notification::make()
                        ->title('Missing schedule parameters')
                        ->body('Slot duration (minutes per slot) is required to generate timeslots.')
                        ->danger()
                        ->send();
                    return;
                }
                
                \Filament\Notifications\Notification::make()
                    ->title('Generating timeslots')
                    ->body("Using parameters: Start: {$record->schedule_starting_at}, End: {$record->schedule_ending_at}, Day Start: {$record->day_starting_at}, Day End: {$record->day_ending_at}, Interval: {$record->minutes_per_slot} minutes")
                    ->info()
                    ->send();
                
                Artisan::call('app:generate-timeslots', [
                    '--start-date' => $record->schedule_starting_at,
                    '--end-date' => $record->schedule_ending_at,
                    '--year-id' => $currentYear->id,
                    '--day-start' => $record->day_starting_at,
                    '--day-end' => $record->day_ending_at,
                    '--lunch-start' => $record->lunch_starting_at,
                    '--lunch-end' => $record->lunch_ending_at,
                    '--interval' => $record->minutes_per_slot,
                ]);
                
                // Get the output from the command to display to the user
                $output = Artisan::output();
                
                // Create a success notification with details
                \Filament\Notifications\Notification::make()
                    ->title('Timeslots generated successfully')
                    ->success()
                    ->send();
            });

        return $static;
    }
}
