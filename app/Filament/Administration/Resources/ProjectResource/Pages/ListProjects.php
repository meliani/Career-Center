<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Enums\Program;
use App\Filament\Actions\Action\Processing\ScheduleProfessorDefensesAction;
use App\Filament\Actions\BulkAction\ScheduleProfessorDefensesBulkAction;
use App\Filament\Administration\Resources\ProjectResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Professor;
use App\Models\Year;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Support\Facades\Artisan;

class ListProjects extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ProjectResource::class;
    
    protected static string $view = 'filament.administration.resources.project-resource.pages.list-projects';

    public ?string $defenseStatusTab = null;

    public function getDefaultLayoutView(): string
    {
        return 'list';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('scheduleProfessorDefenses')
                ->label('Schedule Professor Defenses')
                ->icon('heroicon-o-calendar')
                ->modalHeading('Schedule Defenses by Professor')
                ->modalDescription('Use this tool to schedule defenses for a specific professor in a given time range. You can include or exclude specific dates.')
                ->modalSubmitActionLabel('Schedule Defenses')
                ->form([
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
                ])
                ->action(function (array $data) {
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
                })
                ->visible(fn () => auth()->user()->isAdministrator()),
        ];
    }

    public function getTabs(): array
    {
        // Return the programming status tabs as the primary tabs
        return $this->getProgrammingStatusTabs();
    }

    public function getProgrammingStatusTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery()->active();
        $tabs = [];

        // Allow all authorized users to see programming status tabs
        if (auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor()) {
            // All Projects tab
            $tabs['all'] = Tab::make(__('All Projects'))
                ->badge(
                    $baseQuery->clone()
                        ->whereHas('agreements', function ($query) {
                            $query->whereMorphRelation(
                                'agreeable',
                                [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                'year_id',
                                Year::current()->id
                            );
                        })->count()
                );

            // Programmed Projects tab (projects with timetable)
            $tabs['programmed'] = Tab::make(__('Programmed'))
                ->badge(
                    $baseQuery->clone()
                        ->whereHas('agreements', function ($query) {
                            $query->whereMorphRelation(
                                'agreeable',
                                [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                'year_id',
                                Year::current()->id
                            );
                        })
                        ->whereHas('timetable')
                        ->count()
                )
                ->badgeColor('info')
                ->modifyQueryUsing(
                    fn ($query) => $query->whereHas('timetable')
                );

            // Not Programmed Projects tab (projects without timetable)
            $tabs['not_programmed'] = Tab::make(__('Not Programmed'))
                ->badge(
                    $baseQuery->clone()
                        ->whereHas('agreements', function ($query) {
                            $query->whereMorphRelation(
                                'agreeable',
                                [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                'year_id',
                                Year::current()->id
                            );
                        })
                        ->whereDoesntHave('timetable')
                        ->count()
                )
                ->badgeColor('warning')
                ->modifyQueryUsing(
                    fn ($query) => $query->whereDoesntHave('timetable')
                );
        }

        return $tabs;
    }

    public function getDefenseStatusTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery()->active();
        $tabs = [];

        // Allow all authorized users to see defense status tabs
        if (auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor()) {
            // Defense Status specific tabs
            foreach (\App\Enums\DefenseStatus::cases() as $status) {
                $label = $status->getLabel();
                $value = $status->value;
                $color = $status->getColor();

                $tabs[$value] = Tab::make($label)
                    ->badge(
                        $baseQuery->clone()
                            ->whereHas('agreements', function ($query) {
                                $query->whereMorphRelation(
                                    'agreeable',
                                    [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                    'year_id',
                                    Year::current()->id
                                );
                            })
                            ->where('defense_status', $value)
                            ->count()
                    )
                    ->badgeColor($color)
                    ->modifyQueryUsing(
                        fn ($query) => $query->where('defense_status', $value)
                    );
            }
        }

        return $tabs;
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = static::getResource()::getEloquentQuery();
        
        // Apply programming status filter (from primary tabs)
        $activeTab = $this->activeTab ?? 'all';
        if ($activeTab === 'programmed') {
            $query->whereHas('timetable');
        } elseif ($activeTab === 'not_programmed') {
            $query->whereDoesntHave('timetable');
        }
        
        // Apply defense status filter (from secondary tabs)
        if ($this->defenseStatusTab) {
            $query->where('defense_status', $this->defenseStatusTab);
        }
        
        return $query;
    }
}
