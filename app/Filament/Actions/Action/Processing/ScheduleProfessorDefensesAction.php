<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\Professor;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class ScheduleProfessorDefensesAction extends Action
{
    /**
     * Get the form schema for this action
     * 
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('professor_id')
                ->label('Professor')
                ->options(function() {
                    return Professor::query()
                        ->orderBy('last_name')
                        ->orderBy('first_name')
                        ->get()
                        ->pluck('full_name', 'id');
                })
                ->required()
                ->searchable()
                ->helperText('Select the professor to schedule defenses for'),
                
            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->default(now())
                ->required()
                ->helperText('The date to start scheduling from'),
                
            Forms\Components\DatePicker::make('end_date')
                ->label('End Date')
                ->default(now()->addDays(7))
                ->required()
                ->after('start_date')
                ->helperText('The date to end scheduling at'),
                
            Forms\Components\TagsInput::make('exclude_dates')
                ->label('Exclude Dates')
                ->helperText('Enter dates to exclude from scheduling (format: YYYY-MM-DD)')
                ->placeholder('YYYY-MM-DD'),
                
            Forms\Components\Select::make('max_defenses_per_day')
                ->label('Max Defenses Per Day')
                ->options([
                    1 => '1 defense per day',
                    2 => '2 defenses per day',
                    3 => '3 defenses per day',
                    4 => '4 defenses per day',
                    5 => '5 defenses per day',
                ])
                ->default(3)
                ->required()
                ->helperText('Maximum number of defenses to schedule for this professor per day'),
                
            Forms\Components\Select::make('program')
                ->label('Program Filter')
                ->options(\App\Enums\Program::class)
                ->placeholder('All Programs')
                ->helperText('Optionally filter projects by specific program'),
        ];
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()
            // Add a form to collect scheduling parameters
            ->form($static->getFormSchema())
            ->action(function (array $data): void {
                $currentYear = Year::current();
                $excludeDates = isset($data['exclude_dates']) ? implode(',', $data['exclude_dates']) : null;
                
                // Format dates
                $startDate = date('Y-m-d', strtotime($data['start_date']));
                $endDate = date('Y-m-d', strtotime($data['end_date']));
                
                $commandParams = [
                    '--professor-id' => $data['professor_id'],
                    '--start-date' => $startDate,
                    '--end-date' => $endDate,
                    '--exclude-dates' => $excludeDates,
                    '--max-defenses-per-day' => $data['max_defenses_per_day'],
                    '--year-id' => $currentYear->id,
                ];
                
                // Add program filter if selected
                if (!empty($data['program'])) {
                    $commandParams['--program'] = $data['program'];
                }
                
                $exitCode = Artisan::call('app:schedule-professor-defenses', $commandParams);
                
                // Get the command output
                $output = Artisan::output();
                
                if ($exitCode === 0) {
                    Notification::make()
                        ->title('Professor defenses scheduled successfully')
                        ->success()
                        ->body($output)
                        ->send();
                } else {
                    Notification::make()
                        ->title('Error scheduling professor defenses')
                        ->danger()
                        ->body($output)
                        ->send();
                }
            });

        return $static;
    }
}
