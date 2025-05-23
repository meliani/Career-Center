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
                'startDate' => $record->schedule_starting_at, // Default to schedule period start
                'endDate' => $record->schedule_ending_at,     // Default to schedule period end
                'scheduleId' => $record->id,
                'program' => null, // Default to null (all programs)
                'scheduleStartDate' => $record->schedule_starting_at, // The actual schedule period limits
                'scheduleEndDate' => $record->schedule_ending_at,     // The actual schedule period limits
            ])
            ->form(
                [
                    Forms\Components\Toggle::make('force')->default(false)
                        ->label("Assign the project to the timeslot even if the project's end date is greater than the timeslot's start time."),

                    Forms\Components\Section::make("Define which projects to schedule")
                        ->schema([
                            Forms\Components\DatePicker::make('startDate')
                                ->required()
                                ->label('Project End Date From')
                                ->helperText('Only include projects ending after this date'),
                            Forms\Components\DatePicker::make('endDate')
                                ->required()
                                ->label('Project End Date To')
                                ->helperText('Only include projects ending before this date'),
                            Forms\Components\Hidden::make('scheduleId'),
                            Forms\Components\Hidden::make('scheduleStartDate'),
                            Forms\Components\Hidden::make('scheduleEndDate'),
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
                $scheduleId = $data['scheduleId'] ?? $record->id;
                
                // Get the schedule period limits (when the timeslots should be)
                $scheduleStartDate = $data['scheduleStartDate'] ?? $record->schedule_starting_at;
                $scheduleEndDate = $data['scheduleEndDate'] ?? $record->schedule_ending_at;

                // Build artisan command arguments
                $args = [
                    'projects_start_date' => $startDate,
                    'projects_end_date' => $endDate,
                    '--force' => $force,
                    '--user' => $user,
                    '--schedule' => $scheduleId,
                    '--day-start' => $record->day_starting_at,
                    '--day-end' => $record->day_ending_at,
                    '--lunch-start' => $record->lunch_starting_at,
                    '--lunch-end' => $record->lunch_ending_at,
                    '--interval' => $record->minutes_per_slot,
                    '--schedule-start-date' => $scheduleStartDate,
                    '--schedule-end-date' => $scheduleEndDate,
                ];

                // Add program filter if specified
                if ($program) {
                    $args['--program'] = $program;
                }

                // Execute the command and capture output
                Artisan::call('app:generate-planning', $args);
                $output = Artisan::output();
                
                // Provide detailed feedback based on the command output
                if (strpos($output, 'NO TIMESLOTS FOUND AT ALL') !== false) {
                    Notification::make()
                        ->title('No timeslots available')
                        ->body('Please generate timeslots first using the "Generate Timeslots" action')
                        ->danger()
                        ->send();
                    return;
                }
                
                if (strpos($output, 'No projects found to schedule') !== false) {
                    Notification::make()
                        ->title('No projects to schedule')
                        ->body('No projects found in the selected date range. Try adjusting your date range or check if projects exist.')
                        ->warning()
                        ->send();
                    return;
                }
                
                // Extract scheduling results for better feedback
                preg_match('/Scheduling complete: (\d+) projects scheduled, (\d+) projects could not be scheduled/', $output, $matches);
                
                if (!empty($matches) && isset($matches[1]) && isset($matches[2])) {
                    $scheduled = (int)$matches[1];
                    $notScheduled = (int)$matches[2];
                    
                    if ($scheduled > 0) {
                        $scheduleStartDate = $data['scheduleStartDate'] ?? $record->schedule_starting_at;
                        $scheduleEndDate = $data['scheduleEndDate'] ?? $record->schedule_ending_at;
                        
                        Notification::make()
                            ->title("Timetable generated: {$scheduled} projects scheduled")
                            ->body(
                                ($notScheduled > 0 ? "{$notScheduled} projects could not be scheduled due to conflicts. " : "All projects were successfully scheduled. ") .
                                "Scheduling was limited to timeslots between {$scheduleStartDate} and {$scheduleEndDate}."
                            )
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No projects could be scheduled')
                            ->body('Check that there are available timeslots within the schedule period and rooms, or try with the force option.')
                            ->danger()
                            ->send();
                    }
                } else {
                    // Default notification
                    Notification::make()
                        ->title('Timetable generation complete')
                        ->warning()
                        ->send();
                }
            });

        return $static;
    }
}
