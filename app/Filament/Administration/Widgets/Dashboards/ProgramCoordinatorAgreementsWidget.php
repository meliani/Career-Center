<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums;
use App\Models\FinalYearInternshipAgreement as Agreement;
use App\Services\FastTextService;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ProgramCoordinatorAgreementsWidget extends BaseWidget
{
    protected FastTextService $fastTextService;

    public function __construct(FastTextService $fastTextService = new FastTextService)
    {
        // parent::__construct();
        $this->fastTextService = $fastTextService;
    }

    public ?string $activeFilter = null;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected $listeners = [
        'confirm-department-change' => 'confirmDepartmentChange',
        'cancel-department-change' => 'cancelDepartmentChange',
    ];

    public $pendingDepartmentChange = null;

    public function filterByStat(?string $filter): void
    {
        $this->activeFilter = $this->activeFilter === $filter ? null : $filter;
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        $query = Agreement::query()
            ->with(['student', 'organization'])
            ->whereHas('student', function (Builder $query) {
                $query->where('program', auth()->user()->assigned_program->value);
            });

        return match ($this->activeFilter) {
            'pending' => $query
            // ->where('status', Enums\Status::Signed)
                ->whereNull('assigned_department'),
            'assigned' => $query->whereNotNull('assigned_department')
                ->where('assigned_department', '!=', ''),
            default => $query,
        };
    }

    protected function handleDepartmentChange($record, $newDepartment): void
    {
        $record->department_assigned_at = now();
        $record->department_assigned_by = auth()->user()->id;
        $record->assigned_department = $newDepartment;
        $record->save();

        // Add this line to emit the event
        $this->dispatch('departmentAssigned');

        Notification::make()
            ->title(__('Department Assigned'))
            ->body(__(
                'The department :department has been assigned to the student :student.',
                [
                    'department' => $record->assigned_department->getLabel(),
                    'student' => $record->student->name,
                ]
            ))
            ->success()
            ->send();
    }

    protected function getPredictedDepartment($text): string
    {
        try {
            // Request more predictions to ensure at least three
            $predictions = $this->fastTextService->predictDepartment($text, 3);
            if (! empty($predictions)) {
                // Sort predictions by score descending
                arsort($predictions);

                $result = '🤖 ' . __('Department prediction') . ': ';
                $count = 0;
                foreach ($predictions as $department => $score) {
                    if ($count >= 3) {
                        break;
                    }
                    $probability = number_format($score * 100, 0, '.', '');
                    $result .= \App\Enums\Department::from($department)->getLabel() . " ({$probability}%) ";
                    $count++;
                }

                return trim($result);
            }
        } catch (\Exception $e) {
            return 'Could not predict department';
        }

        return 'No prediction available';
    }

    public function table(Table $table): Table
    {
        // $filterLabel = match ($this->activeFilter) {
        //     'pending' => __('Pending Assignment'),
        //     'assigned' => __('Department Assigned'),
        //     default => null,
        // };

        return $table
            // ->heading($filterLabel)
            ->heading(false)
            ->headerActions([
                \Filament\Tables\Actions\Action::make('help')
                    ->label('')
                    ->tooltip(false)
                    ->button()
                    ->view('filament.widgets.program-coordinator.help-message'),

                \Filament\Tables\Actions\Action::make('stats')
                    ->label('')
                    ->tooltip(false)
                    ->button()
                    ->view('filament.widgets.program-coordinator.stats', [
                        'stats' => $this->getStats(),
                        'activeFilter' => $this->activeFilter,
                    ]),
            ])
            ->query(fn () => $this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->organization->name)
                    ->extraAttributes([
                        'class' => 'animate-fade-in',
                        'wire:loading.class' => 'opacity-50',
                    ]),
                // ->tooltip(fn ($record) => __('Project title') . ': ' . $record->title),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'animate-slide-in-right'])
                    ->tooltip(function ($record) {
                        return __('Project description') . ': ' . $record->description;
                    }),

                // Tables\Columns\TextColumn::make('organization.name')
                //     ->label(__('Organization'))
                //     ->searchable()
                //     ->sortable()
                //     ->extraAttributes(['class' => 'animate-slide-in-right']),

                // \Guava\FilamentIconSelectColumn\Tables\Columns\IconSelectColumn::make('status')
                //     ->label(__('Status'))
                //     ->options(Enums\Status::class)
                //     ->searchable()
                //     ->sortable()
                //     ->extraAttributes(['class' => 'animate-fade-in'])
                //     ->icon(fn ($record) => $record->status->getIcon())
                //     ->color(fn ($record) => $record->status->getColor()),

                \Archilex\ToggleIconColumn\Columns\ToggleIconColumn::make('validated_at')
                    ->label(__('Validation'))
                    ->searchable()
                    ->sortable()
                    ->hoverColor('success')
                    ->extraAttributes(['class' => 'animate-fade-in'])
                    ->tooltip(function ($record) {
                        return $record->validated_at ? __('Validated - click to unvalidate') : __('Unvalidated - click to validate');
                    }),

                Tables\Columns\SelectColumn::make('assigned_department')
                    ->options(function () {
                        // departments with count

                        $departments = collect(Enums\Department::cases())
                            ->mapWithKeys(function ($department) {
                                $count = Agreement::where('assigned_department', $department->value)->count();

                                return [$department->value => "{$department->value} ({$count} " . __('projects') . ')'];
                            });

                        return $departments;

                    })
                    ->tooltip(function ($record) {
                        $text = $record->title . ' ' . $record->description;
                        $prediction = $this->getPredictedDepartment($text);

                        return $prediction . "\n\n" . __('(Training data from previous year assingments)') .
                            '. ' . __('This feature is experimental and may not be accurate.');
                    })
                    ->label(__('Department'))
                    // ->options(Enums\Department::class)
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'transition-all duration-300 hover:scale-105',
                    ])
                    ->updateStateUsing(fn ($state, $record) => $record->assigned_department) // Disable automatic change
                    ->beforeStateUpdated(function ($record, $state) {
                        if (! $record->assigned_department) {
                            $this->handleDepartmentChange($record, $state);

                            return true;
                        }

                        $this->pendingDepartmentChange = [
                            'recordId' => $record->id,
                            'department' => $state,
                        ];

                        Notification::make('department-reassignment')
                            ->title(__('Department Reassignment'))
                            ->body(__(
                                'The student :student is being reassigned from :oldDepartment to :newDepartment.',
                                [
                                    'student' => $record->student->name,
                                    'oldDepartment' => $record->assigned_department->getLabel(),
                                    'newDepartment' => Enums\Department::from($state)->getLabel(),
                                ]
                            ))
                            ->warning()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('confirm')
                                    ->label(__('Confirm Change'))
                                    ->button()
                                    ->color('warning')
                                    ->dispatch('confirm-department-change', [
                                        'id' => $record->id,
                                        'department' => $state,
                                    ])
                                    ->close(),
                                \Filament\Notifications\Actions\Action::make('cancel')
                                    ->label(__('Cancel'))
                                    ->color('gray')
                                    ->dispatch('cancel-department-change')
                                    ->close(),
                            ])
                            // ->persistent()
                            ->send();

                        return false;
                    }),
                // ->tooltip(fn ($record) => $record->suggestedInternalSupervisor ? __('Internal Supervisor (student suggested)') . ': ' . $record->suggestedInternalSupervisor->name : null),
                // Tables\Columns\TextColumn::make('suggestedInternalSupervisor.name')
                //     ->label(__('Internal Supervisor (student suggested)')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Submitted'))
                    ->dateTime()
                    ->sortable()
                    ->extraAttributes(['class' => 'animate-fade-in']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->label(__('Department'))
                    ->options(Enums\Department::class),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label(false)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Agreement $record) => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $record]))
                    ->extraAttributes(['class' => 'transition-transform duration-300 hover:scale-110']),
                Tables\Actions\ViewAction::make()
                    ->label(false)
                    ->icon('heroicon-o-magnifying-glass')
                    ->modalContent(fn (Agreement $record): View => view(
                        'filament.resources.final-year-internship-agreement-resource.modal.view',
                        ['record' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->extraAttributes(['class' => 'transition-transform duration-300 hover:scale-110']),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            // ->recordUrl(
            //     fn (Agreement $record): string => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $record])
            // )
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', null));
    }

    protected function getStats(): array
    {
        $baseQuery = Agreement::query()
            ->whereHas('student', function (Builder $query) {
                $query->where('program', auth()->user()->assigned_program->value);
            });

        return [
            [
                'label' => __('Total Agreements'),
                'value' => $baseQuery->count(),
                'icon' => 'heroicon-o-document-text',
                'color' => 'primary',
                'filter' => 'all',
            ],
            [
                'label' => __('Pending Department Assignment'),
                'value' => (clone $baseQuery)
                    // ->where('status', Enums\Status::Signed)
                    ->whereNull('assigned_department')
                    ->orWhere('assigned_department', '')
                    ->count(),
                'icon' => 'heroicon-o-clock',
                'color' => 'warning',
                'filter' => 'pending',
            ],
            [
                'label' => __('Department Assigned'),
                'value' => (clone $baseQuery)
                    ->whereNotNull('assigned_department')
                    ->where('assigned_department', '!=', '')
                    ->count(),
                'icon' => 'heroicon-o-check-badge',
                'color' => 'success',
                'filter' => 'assigned',
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole(Enums\Role::ProgramCoordinator);
    }

    protected function getTableContentFooter(): ?View
    {
        return view('filament.widgets.program-coordinator.footer');
    }

    public function confirmDepartmentChange(int $id, string $department): void
    {
        if ($record = Agreement::find($id)) {
            $this->handleDepartmentChange($record, $department);
            // Add this line to emit the event
            $this->dispatch('departmentChanged');
        }
        $this->pendingDepartmentChange = null;
    }

    public function cancelDepartmentChange(): void
    {
        $this->pendingDepartmentChange = null;
        $this->resetTable();
    }
}
