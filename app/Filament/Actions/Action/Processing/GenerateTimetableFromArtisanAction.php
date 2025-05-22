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
                'program' => null, // Default to null (all programs)
            ])
            ->form(
                [
                    Forms\Components\Toggle::make('force')->default(false)
                        ->label("Assign the project to the timeslot even if the project's end date is greater than the timeslot's start time."),

                    Forms\Components\Section::make("Define the project's date range that will be scheduled and planned within the specified schedule window")
                        ->schema([
                            Forms\Components\DatePicker::make('startDate')->required(),
                            Forms\Components\DatePicker::make('endDate')->required(),
                            Forms\Components\Select::make('program')
                                ->label('Filter by Program')
                                ->options(\App\Enums\Program::class)
                                ->placeholder('All Programs')
                                ->helperText('Only schedule projects for students in this program'),
                        ]),
                ]
            )
            ->action(function (array $data, $record): void {

                $force = $data['force'] ? '--force' : '';
                $user = 1;

                // Access the form data from the $record parameter
                $startDate = $data['startDate'];
                $endDate = $data['endDate'];
                $program = $data['program'];

                // Build artisan command arguments
                $args = [
                    'projects_start_date' => $startDate,
                    'projects_end_date' => $endDate,
                    '--force' => $force,
                    '--user' => $user,
                    '--schedule' => $record->id,
                ];

                // Add program filter if specified
                if ($program) {
                    $args['--program'] = $program;
                }

                Artisan::call('app:generate-planning', $args);

                Notification::make()
                    ->title('Projects Timetable has been generated successfully')
                    ->success()
                    ->send();
            });

        return $static;
    }
}
