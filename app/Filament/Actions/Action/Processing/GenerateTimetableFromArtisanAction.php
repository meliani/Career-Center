<?php

namespace App\Filament\Actions\Action\Processing;

use Filament\Forms;
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

        $static->configure()
            ->fillform(fn ($record) => [
                'force' => false,
                'startDate' => $record->schedule_starting_at,
                'endDate' => $record->schedule_ending_at,
            ])
            ->form(
                [
                    Forms\Components\Toggle::make('force')->default(false)
                        ->label("Assign the project to the timeslot even if the project's end date is greater than the timeslot's start time."),

                    Forms\Components\Section::make("Define the project's date range that will be scheduled and planned within the specified schedule window")
                        ->schema([
                            Forms\Components\DatePicker::make('startDate')->required(),
                            Forms\Components\DatePicker::make('endDate')->required(),
                        ]),
                ]
            )
            ->action(function (array $data, $record): void {

                $force = $data['force'] ? '--force' : '';
                $user = 1;

                // Access the form data from the $record parameter
                $startDate = $data['startDate'];
                $endDate = $data['endDate'];

                // dd($startDate, $endDate);

                Artisan::call('app:generate-planning', [
                    'projects_start_date' => $startDate,
                    'projects_end_date' => $endDate,
                    '--force' => $force,
                    '--user' => $user,
                    '--schedule' => $record->id,
                ]);

                Notification::make()
                    ->title('Projects Timetable has been generated successfully')
                    ->success()
                    ->send();
            });

        return $static;
    }
}
