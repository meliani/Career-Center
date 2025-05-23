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
                
                // Format the dates for clearer output
                $startDate = $record->schedule_starting_at->format('Y-m-d');
                $endDate = $record->schedule_ending_at->format('Y-m-d');
                $dayStart = $record->day_starting_at->format('H:i:s');
                $dayEnd = $record->day_ending_at->format('H:i:s');
                $lunchStart = $record->lunch_starting_at->format('H:i:s');
                $lunchEnd = $record->lunch_ending_at->format('H:i:s');
                $interval = $record->minutes_per_slot;
                
                \Filament\Notifications\Notification::make()
                    ->title('Generating timeslots')
                    ->body("Using parameters: Start: {$startDate}, End: {$endDate}, Day: {$dayStart}-{$dayEnd}, Lunch: {$lunchStart}-{$lunchEnd}, Interval: {$interval} minutes")
                    ->info()
                    ->send();
                
                Artisan::call('app:generate-timeslots', [
                    '--start-date' => $startDate,
                    '--end-date' => $endDate,
                    '--year-id' => $currentYear->id,
                    '--day-start' => $dayStart,
                    '--day-end' => $dayEnd,
                    '--lunch-start' => $lunchStart,
                    '--lunch-end' => $lunchEnd,
                    '--interval' => $interval,
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
