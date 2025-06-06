<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Actions\BulkAction\ScheduleProfessorDefensesBulkAction;
use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Project;
use App\Models\Year;
use App\Notifications\CollaborationReminderNotification;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Guava\FilamentModalRelationManagers\Actions\Infolist\RelationManagerAction as InfolistRelationManagerAction;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Hydrat\TableLayoutToggle\Facades\TableLayoutToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel;
use App\Settings\DisplaySettings;
use App\Filament\Actions\Action\SeparateBinomeAction;
class ProjectResource extends Core\BaseResource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Final project';

    protected static ?string $pluralModelLabel = 'Final projects';

    protected static ?string $title = 'title';

    protected static ?string $recordTitleAttribute = 'students_names';

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?int $navigationSort = 4;

    protected static ?int $sort = 4;

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->isAdministrator()) {
            return static::getModel()::active()->count();
        }

        return null;
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            // 'title',
            'agreements.organization.name',
            // 'agreements.student.first_name',
            // 'agreements.student.last_name',
            // 'agreements.agreeable.student.id_pfe',
            // 'professors.first_name',
            // 'professors.last_name',
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor();
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor();
        }

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['agreements.agreeable.student', 'timetable.timeslot', 'timetable.room']);

        $user = auth()->user();

        // Administrators and Administrative Supervisors can see all projects
        if ($user->isAdministrator() || $user->isAdministrativeSupervisor()) {
            return $query;
        }

        // Program Coordinators can see projects in their assigned program
        if ($user->isProgramCoordinator()) {
            return $query->whereHas('final_internship_agreements', function ($query) use ($user) {
                $query->whereHas('student', function ($query) use ($user) {
                    $query->where('program', $user->assigned_program);
                });
            });
        }

        // Department Heads can see projects in their department
        if ($user->isDepartmentHead()) {
            return $query->whereHas('final_internship_agreements', function ($query) use ($user) {
                $query->where('assigned_department', $user->department);
            });
        }

        // Professors can see their own projects
        if ($user->isProfessor()) {
            return $query->active()->whereHas('professors', function ($query) use ($user) {
                $query->where('professor_id', $user->id);
            });
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProfessorsRelationManager::class,
            RelationGroup::make(__('Students'), [
                // RelationManagers\StudentsRelationManager::class,
                RelationManagers\InternshipAgreementsRelationManager::class,
                // RelationManagers\CommentsRelationManager::class,
            ]),
            RelationGroup::make(__('Defense Details'), [
                RelationManagers\TimetableRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            // 'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => Pages\ViewProject::route('/{record}/view'),

        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Project information')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\MarkdownEditor::make('title')
                            ->disabled(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false)
                            ->required()
                            ->maxLength(255)
                            ->columnspan(2),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->disabled(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false)
                            ->disabled(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                        Forms\Components\DatePicker::make('midterm_due_date')
                            ->label(__('Midterm Due Date'))
                            ->native(false)
                            ->disabled(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                        Forms\Components\Select::make('midterm_report_status')
                            ->label(__('Midterm Report Status'))
                            ->options(\App\Enums\MidTermReportStatus::class)
                            ->disabled(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                    ])
                    ->collapsible()
                    ->columns(2),
                Forms\Components\Section::make(__('Defense information'))
                    ->label(__('Defense information'))
                    ->columnSpan(1)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->relationship('timetable') // Changed from 'currentYearTimetable' to just 'timetable'
                    ->hidden(true)
                    ->columns(3)
                    ->schema([
                        // Forms\Components\Fieldset::make('Time')
                        //     ->relationship('timeslot')
                        //     ->schema([
                        //         Forms\Components\DateTimePicker::make('start_time')
                        //             // ->format('m/d/Y hh:ii:ss')
                        //             ->seconds(false)
                        //             ->native(false)
                        //             ->displayFormat('Y-m-d H:i:s')
                        //             ->required(),
                        //         // ->disabled(fn () => auth()->user()->isAdministrator() === false),
                        //         Forms\Components\DateTimePicker::make('end_time')
                        //             ->seconds(false)
                        //             // ->native(false)
                        //             ->displayFormat('Y-m-d H:i:s')
                        //             ->required(),
                        //         // ->disabled(fn () => auth()->user()->isAdministrator() === false),
                        //     ]),
                        Forms\Components\Select::make('timeslot_id')
                            ->relationship('timeslot', 'start_time')
                            // ->required()
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                        Forms\Components\Select::make('room_id')
                            ->relationship('room', 'name')
                            // ->required()
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state))

                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                    ])
                    ->columns(3),

            ]);
    }

    private static function compareTitles($project): array
    {
        $agreementTitle = $project->final_internship_agreements->first()?->title ?? '';
        $projectTitle = $project->title ?? '';

        $matches = trim(strtolower($agreementTitle)) === trim(strtolower($projectTitle));

        return [
            'matches' => $matches,
            'agreement_title' => $agreementTitle,
            'project_title' => $projectTitle,
        ];
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->defaultPaginationPageOption(10)
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            // ->striped()
            // ->deferLoading()
            ->defaultSort('timetable.timeslot.start_time', 'asc')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->leftJoin('timetables', 'projects.id', '=', 'timetables.project_id')
                ->leftJoin('timeslots', 'timetables.timeslot_id', '=', 'timeslots.id')
                ->orderByRaw('timeslots.start_time IS NULL, timeslots.start_time ASC')
                ->select('projects.*')
            )
            // ->defaultGroup('timetable.timeslot.start_time')
            ->groups([
                // Tables\Grouping\Group::make('timetable.timeslot.start_time')
                //     ->date()
                //     ->collapsible()
                //     ->label(__('Day of')),
                // Tables\Grouping\Group::make('defense_status')
                //     ->collapsible()
                //     ->label(__('Defense status')),
            ])
            ->columns([
                ...(
                    $livewire->isGridLayout()
                        ? \App\Services\Filament\Tables\Projects\ProjectsGrid::get()
                        : \App\Services\Filament\Tables\Projects\ProjectsTable::get()
                ),
                Tables\Columns\TextColumn::make('midterm_due_date')
                    ->label(__('Midterm Due Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('midterm_report_status')
                    ->label(__('Midterm Status'))
                    ->badge()
                    ->sortable(),
            ])
            ->contentGrid(
                fn () => $livewire->isGridLayout()
                    ? [
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 4,
                    ] : null
            )
            ->filters([
                // SelectFilter::make('agreement_type')
                //     ->label('Internship Type')
                //     ->options([
                //         'internship' => 'Introductory/Technical Internship',
                //         'final_year' => 'Final Year Internship',
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query->when(
                //             $data['value'],
                //             function (Builder $query, string $value): Builder {
                //                 return $query->whereHas('agreements', function (Builder $query) use ($value) {
                //                     if ($value === 'internship') {
                //                         $query->where('agreeable_type', InternshipAgreement::class);
                //                     } elseif ($value === 'final_year') {
                //                         $query->where('agreeable_type', FinalYearInternshipAgreement::class);
                //                     }
                //                 });
                //             }
                //         );
                //     })
                //     ->indicateUsing(function (array $data): ?string {
                //         if (! $data['value']) {
                //             return null;
                //         }

                //         return 'Type: ' . (
                //             $data['value'] === 'internship'
                //             ? 'Introductory/Technical'
                //             : 'Final Year'
                //         );
                //     })
                //     ->visible(fn () => auth()->user()->isAdministrator() === true),

                // DateRangeFilter::make('timetable.timeslot.start_time')
                // ->label('Defense date')
                // ->defaultToday()
                // ->timePicker()
                // ->modifyQueryUsing(fn (Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) => $query->whereHas('timetable.timeslot', fn ($query) => $query->whereBetween('start_time', [$startDate, $endDate]))),
                // Tables\Filters\SelectFilter::make('timetable.timeslot.start_time')
                //     ->label('Defense date')
                //     ->relationship('timetable.timeslot', 'start_time')
                //     ->searchable()
                //     ->preload(),
                // Tables\Filters\Filter::make('Defense date')
                //     ->form([
                //         Forms\Components\DatePicker::make('defenses_from'),
                //         // ->default(now()),
                //         Forms\Components\DatePicker::make('defenses_until'),
                //         // ->default(now()->addDays(7)),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['defenses_from'],
                //                 fn (Builder $query, $date): Builder => $query->whereRelation('timetable.timeslot', 'start_time', '>=', $date),
                //             )
                //             ->when(
                //                 $data['defenses_until'],
                //                 fn (Builder $query, $date): Builder => $query->whereRelation('timetable.timeslot', 'start_time', '<=', $date),
                //             );
                //     })
                //     ->indicateUsing(function (array $data): array {
                //         $indicators = [];

                //         if ($data['defenses_from'] ?? null) {
                //             $indicators[] = Tables\Filters\Indicator::make(__('Defenses from :date', ['date' => Carbon::parse($data['defenses_from'])->toFormattedDateString()]))
                //                 ->removeField('defenses_from');
                //         }

                //         if ($data['defenses_until'] ?? null) {
                //             $indicators[] = Tables\Filters\Indicator::make(__('Defenses until :date', ['date' => Carbon::parse($data['defenses_until'])->toFormattedDateString()]))
                //                 ->removeField('defenses_until');
                //         }

                //         return $indicators;
                //     }),
                // ->default(),

                // Tables\Filters\SelectFilter::make('defense_status')
                //     ->options(Enums\DefenseStatus::class)
                //     ->query(
                //         fn (Builder $query, array $data) => $query->when(
                //             $data['value'],
                //             fn (Builder $query, $status): Builder => $query->where('defense_status', $status)
                //         ),
                //     ),
                Tables\Filters\SelectFilter::make('professor')
                    ->searchable()
                    ->preload()
                    ->options(fn () => \App\Models\Professor::all()->pluck('full_name', 'id'))
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $professor): Builder => $query->whereHas('professors', fn (Builder $query) => $query->where('professor_id', $professor))
                        ),
                    ),
                Tables\Filters\SelectFilter::make('program')
                    ->label(__('Program'))
                    ->options(Enums\Program::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $program): Builder => $query->whereHas('agreements', function ($query) use ($program) {
                                $query->whereHas('agreeable', function ($q) use ($program) {
                                    $q->whereHas('student', fn ($q) => $q->where('program', $program));
                                });
                            })
                        ),
                    ),
                Tables\Filters\SelectFilter::make('defense_status')
                    ->label(__('Defense Status'))
                    ->options(Enums\DefenseStatus::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $status): Builder => $query->where('defense_status', $status)
                        ),
                    ),
                Tables\Filters\SelectFilter::make('Assigned department')
                    ->label(__('Assigned department'))
                    ->options(Enums\Department::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $department): Builder => $query->whereRelation('final_internship_agreements', 'assigned_department', $department)
                        ),
                    ),

                // SelectFilter::make('agreement_type')
                //     ->label('Agreement Type')
                //     ->options([
                //         'InternshipAgreement' => 'Internship Agreement',
                //         'FinalYearInternshipAgreement' => 'Final Year Internship Agreement',
                //     ])
                //     ->query(function (Builder $query, array $data) {
                //         if ($data['value']) {
                //             $query->whereHas('agreements', function (Builder $q) use ($data) {
                //                 $q->where('agreeable_type', 'App\\Models\\' . $data['value']);
                //             });
                //         }
                //     }),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(Year::getYearsForSelect(1))
                    ->default(Year::current()?->id)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value) => $query->whereHas('agreements', function (Builder $query) use ($value) {
                                $query->whereHasMorph('agreeable', [InternshipAgreement::class, FinalYearInternshipAgreement::class], function (Builder $query) use ($value) {
                                    // $query->whereHas('student', function (Builder $query) use ($value) {
                                    $query->where('year_id', $value);
                                    // });
                                });
                            })
                        );
                    })
                    ->indicateUsing(fn (array $data): ?string => $data['value'] ? __('Year') . ': ' . (Year::find($data['value'])?->title ?? 'Unknown') : null)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                // trashed filter visible by admins
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),

            ])
            ->headerActions([
                /*  Tables\Actions\Action::make('Check changed professors')
                        ->label('Check changed professors')
                        ->tooltip('Check if professors have been changed after getting Authorization')
                        ->color('primary')
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->action(function () {
                            $googleServices = new \App\Services\GoogleServices;
                            $googleServices->checkChangedProfessors();
                        }),
                \App\Filament\Actions\Action\Processing\GoogleSheetSyncAction::make('Google Sheet Sync')
                        ->label('Google Sheet Sync')
                        ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                    */
                FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        FilamentExcel\Exports\ExcelExport::make()
                            ->askForFilename()
                            ->askForWriterType()
                            ->withFilename(fn ($filename) => 'carrieres-' . $filename)
                            ->fromTable()
                            ->ignoreFormatting([
                                'created_at',
                                'updated_at',
                                // 'timetable.timeslot.start_time',
                                // 'timetable.timeslot.end_time',
                                //'start_date',
                                //'end_date',
                            ])
                            ->withColumns([
                                FilamentExcel\Columns\Column::make('title')->width(10),
                                // FilamentExcel\Columns\Column::make('description')->width(10),
                                FilamentExcel\Columns\Column::make('organization.name')->width(10)->heading(__('Organization')),
                                FilamentExcel\Columns\Column::make('academic_supervisor_name')->width(10)->heading(__('Academic Supervisor')),
                                // FilamentExcel\Columns\Column::make('reviewer1')->width(10)->heading(__('First Reviewer')),
                                // FilamentExcel\Columns\Column::make('reviewer2')->width(10)->heading(__('Second Reviewer')),
                                // FilamentExcel\Columns\Column::make('start_date')
                                //     ->heading(__('Internship Start Date'))
                                //     ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
                                // FilamentExcel\Columns\Column::make('end_date')
                                //     ->heading(__('Internship End Date'))
                                //     ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
                                // FilamentExcel\Columns\Column::make('midterm_due_date')
                                //     ->heading(__('Midterm Due Date'))
                                //     ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
                                // FilamentExcel\Columns\Column::make('timetable.timeslot.start_time')
                                //     ->heading(__('Defense Start Time'))
                                //     ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
                                // FilamentExcel\Columns\Column::make('timetable.timeslot.end_time')
                                //     ->heading(__('Defense End Time'))
                                //     ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
                            ]),
                    ])
                    ->tooltip(__('Displayed columns will be exported, you can change the columns to be exported from the table settings')),
                TableLayoutToggle::getToggleViewTableAction(compact: true),
                // Tables\Actions\ActionGroup::make([
                //     \App\Filament\Actions\Action\Processing\ImportProfessorsFromInternshipAgreements::make('Import Professors From Internship Agreements')
                //         ->hidden(fn () => auth()->user()->isAdministrator() === false),
                // ]),

            ])
            ->actions([
                \Parallax\FilamentComments\Tables\Actions\CommentsAction::make()
                    ->label(false)
                    ->tooltip(
                        fn ($record) => "{$record->filamentComments()->count()} " . __('Comments')
                        . ' (' . __('Will be visible only to professors and administrators') . ')'
                    )
                        // ->visible(fn () => true)
                    ->badge(fn ($record) => $record->filamentComments()->count() ? $record->filamentComments()->count() : null),

                Tables\Actions\ActionGroup::make([
                    RelationManagerAction::make('professors-relation-manager')
                        ->label('Jury Members')
                        ->icon('heroicon-o-users')
                        ->relationManager(RelationManagers\ProfessorsRelationManager::class)
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false),

                    RelationManagerAction::make('timetable-relation-manager')
                        ->label('Timetable')
                        ->icon('heroicon-o-clock')
                        ->relationManager(RelationManagers\TimetableRelationManager::class)
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false),
                    RelationManagerAction::make('comments-relation-manager')
                        ->label('Manage Comments')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->relationManager(RelationManagers\CommentsRelationManager::class)
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->modalHeading('Comments')
                        ->modalSubmitAction(false)
                        ->slideOver()
                        ->modalCancelAction(false),

                ])
                    ->icon('heroicon-o-ellipsis-horizontal-circle')
                    ->color('primary')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->outlined()
                    ->label(__('Relation Managers'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label(false)
                    ->hidden(fn ($record) => auth()->user()->can('manage-project', $record) === true),

                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\Action\SendDefenseEmailAction::make()
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->disabled(fn ($record): bool => $record['defense_authorized_at'] == null)
                        // ->disabled(fn ($record): bool => $record['validated_at'] !== null)
                        ->hidden(fn ($record) => auth()->user()->can('send-defense-email', $record) === false),
                    \App\Filament\Actions\Action\AuthorizeDefenseAction::make()
                        ->icon('heroicon-o-check')
                        ->color('success')
                        // ->disabled(fn ($record): bool => $record['validated_at'] !== null)
                        ->hidden(fn ($record) => auth()->user()->can('authorize-defense', $record) === false),
                    Tables\Actions\Action::make('separate_binome')
                        ->label(__('Separate Binome'))
                        ->icon('heroicon-o-scissors')
                        ->color('warning')
                        ->requiresConfirmation(false)
                        ->modalHeading(__('Separate Binome'))
                        ->modalDescription(__('Choose which student will keep the original project with its current supervisor, timetable, and settings. The other students will get new individual projects.'))
                        ->modalSubmitActionLabel(__('Separate'))
                        ->visible(fn ($record) => $record->agreements()->count() > 1)
                        ->hidden(fn ($record) => auth()->user()->isAdministrator() === false)
                        ->form(function ($record) {
                            $agreements = $record->agreements()->with('agreeable.student')->get();
                            $studentOptions = [];
                            
                            foreach ($agreements as $agreement) {
                                $student = $agreement->agreeable->student ?? null;
                                if ($student) {
                                    $studentOptions[$agreement->id] = $student->name . ' (' . $student->id_pfe . ')';
                                }
                            }
                            
                            return [
                                Forms\Components\Select::make('keep_agreement_id')
                                    ->label(__('Student to keep with original project'))
                                    ->options($studentOptions)
                                    ->required()
                                    ->default(array_key_first($studentOptions))
                                    ->helperText(__('This student will keep the original project with its supervisor, timetable, and all settings. Other students will get new projects.')),
                                Forms\Components\Checkbox::make('copy_timetable')
                                    ->label(__('Copy timetable to new projects'))
                                    ->default(true)
                                    ->helperText(__('If checked, the timetable will be copied to the new projects (may need adjustment later).')),
                                Forms\Components\Checkbox::make('copy_professors')
                                    ->label(__('Copy jury members to new projects'))
                                    ->default(true)
                                    ->helperText(__('If checked, the same jury members will be assigned to all new projects.')),
                            ];
                        })
                        ->action(function ($record, array $data) {
                            \Log::info('Separate binome action started for project: ' . $record->id, $data);
                            
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $agreements = $record->agreements()->with('agreeable.student')->get();
                                    
                                    if ($agreements->count() <= 1) {
                                        Notification::make()
                                            ->title(__('Cannot separate'))
                                            ->body(__('This project does not have multiple students to separate.'))
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    $keepAgreementId = $data['keep_agreement_id'];
                                    $copyTimetable = $data['copy_timetable'] ?? true;
                                    $copyProfessors = $data['copy_professors'] ?? true;

                                    // Find the agreement to keep with original project
                                    $keepAgreement = $agreements->where('id', $keepAgreementId)->first();
                                    $moveAgreements = $agreements->where('id', '!=', $keepAgreementId);

                                    if (!$keepAgreement) {
                                        throw new \Exception('Selected student agreement not found.');
                                    }

                                    $newProjects = [];

                                    foreach ($moveAgreements as $agreement) {
                                        // Create a new project for each agreement to move
                                        $newProject = $record->replicate();
                                        $newProject->save();
                                        $newProjects[] = $newProject;

                                        // Move the agreement to the new project
                                        $agreement->update(['project_id' => $newProject->id]);

                                        // Copy professors if requested
                                        if ($copyProfessors) {
                                            foreach ($record->professors as $professor) {
                                                $newProject->professors()->attach($professor->id, [
                                                    'jury_role' => $professor->pivot->jury_role ?? null,
                                                    'created_by' => $professor->pivot->created_by ?? null,
                                                    'updated_by' => $professor->pivot->updated_by ?? null,
                                                    'approved_by' => $professor->pivot->approved_by ?? null,
                                                    'is_president' => $professor->pivot->is_president ?? false,
                                                    'votes' => $professor->pivot->votes ?? null,
                                                    'was_present' => $professor->pivot->was_present ?? false,
                                                ]);
                                            }
                                        }

                                        // Copy timetable if requested and exists
                                        if ($copyTimetable && $record->timetable) {
                                            $newTimetable = $record->timetable->replicate();
                                            $newTimetable->project_id = $newProject->id;
                                            $newTimetable->save();
                                        }

                                        // Copy comments (always copy for continuity)
                                        foreach ($record->filamentComments as $comment) {
                                            $newComment = $comment->replicate();
                                            $newComment->commentable_id = $newProject->id;
                                            $newComment->save();
                                        }
                                    }

                                    \Log::info('Binome separation completed successfully. Created ' . count($newProjects) . ' new projects.');
                                });

                                Notification::make()
                                    ->title(__('Binome separated successfully'))
                                    ->body(__('The binome has been separated into individual projects. The selected student kept the original project.'))
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                \Log::error('Error separating binome: ' . $e->getMessage());
                                \Log::error('Stack trace: ' . $e->getTraceAsString());
                                
                                Notification::make()
                                    ->title(__('Error separating binome'))
                                    ->body(__('An error occurred: ') . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('Postpone')
                        ->label('Postpone defense')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->hidden(fn ($record) => auth()->user()->can('authorize-defense', $record) === false)
                        ->action(fn ($record) => $record->postponeDefense()),

                    // Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make(),
                ])->icon('heroicon-o-bars-3')
                    ->hidden(fn ($record) => auth()->user()->can('manage-project', $record) === false),
                    // ->visible(false),
                // ])->dropdown(false)
                // ->tooltip(__('Edit or view this project')),
                // ->hidden(true),
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->hidden(fn ($record) => auth()->user()->isAdministrator() === false),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('authorize')
                        ->label('Authorize selection')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->authorizeDefense()),
                    Tables\Actions\BulkAction::make('postpone')
                        ->label('Postpone selection')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->postponeDefense()),
                    ScheduleProfessorDefensesBulkAction::make()
                        ->color('primary')
                        ->hidden(fn () => !auth()->user()->isAdministrator()),
                    Tables\Actions\BulkAction::make('complete')
                        ->label('Complete selection')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->completeDefense()),
                    Tables\Actions\BulkAction::make('markAllProfessorsAsPresent')
                        ->label('Mark jury as present')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->size(\Filament\Support\Enums\ActionSize::ExtraLarge)
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->markAllProfessorsAsPresent()),
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                ])
                    ->outlined()
                    ->icon('heroicon-o-ellipsis-horizontal-circle')
                    ->color('primary')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    // ->dropdownWidth(\Filament\Support\Enums\MaxWidth::ExtraSmall)
                    ->label(__('Mass prossessing'))
                    ->hidden(fn () => auth()->user()->cannot('manage-projects')),
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendGenericEmail::make('Send mass emails to students')
                        ->tooltip(__('Send customized generic mass emails to students'))
                        ->label(__('Write mass emails to students'))
                        ->outlined(),
                    BulkAction\Email\SendConnectingSupervisorsEmail::make('SendConnectingSupervisorsEmail')
                        ->label(__('Connection email : Supervisors / Students'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->requiresConfirmation()
                        ->outlined(),
                ])->label(__('Mass Notification Emails'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Large)
                    ->color('secondary')
                    ->icon('heroicon-o-inbox-stack')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->outlined()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkAction::make('sendCollaborationReminder')
                    ->label(__('Send Collaboration Reminder'))
                    ->action(function (Collection $records) {
                        foreach ($records as $project) {
                            foreach ($project->getStudentsCollection() as $student) {
                                $student->notify(new CollaborationReminderNotification($project));
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
            ]);

    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(12)
            ->schema([
                Infolists\Components\Tabs::make('Relations')
                    ->columns(4)
                    ->columnSpan(8)
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Jury Members'))
                            ->columnSpan(4)
                            ->icon('heroicon-o-users')
                            ->schema([
                                Infolists\Components\Section::make(__('Jury Members'))
                                    ->columns(3)
                                    ->icon('heroicon-o-users')
                                    ->headerActions([
                                        InfolistRelationManagerAction::make('professors-relation-manager')
                                            ->label(__('Edit jury members'))
                                            ->relationManager(RelationManagers\ProfessorsRelationManager::class)
                                            ->hidden(fn (Request $request, $record) => (auth()->user()->can('manage-supervision', $record)) === false)
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false),
                                    ])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('academic_supervisor_name')
                                            ->label(__('Academic supervisor'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->badge()
                                            ->color('info'),

                                        Infolists\Components\TextEntry::make('reviewer1')
                                            ->label(__('First Reviewer'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_project_reviewers || auth()->user()->isAdministrator())
                                            ->color('success'),

                                        Infolists\Components\TextEntry::make('reviewer2')
                                            ->label(__('Second Reviewer'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_project_reviewers || auth()->user()->isAdministrator())
                                            ->color('success'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Schedule'))
                            ->columnSpan(4)
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Infolists\Components\Section::make(__('Defense Schedule'))
                                    ->icon('heroicon-o-calendar')
                                    ->columns(3)
                                    ->headerActions([
                                        InfolistRelationManagerAction::make('timetable-relation-manager')
                                            ->label(__('Edit timetable'))
                                            ->relationManager(RelationManagers\TimetableRelationManager::class)
                                            ->hidden(fn (Request $request, $record) => (auth()->user()->can('manage-timetables', [
                                                $record,  // The model as first element
                                                auth()->user(),       // The user as second element
                                            ])) === false)
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false),
                                    ])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('timetable.timeslot.start_time')
                                            ->label('Start time')
                                            ->icon('heroicon-o-clock')
                                            ->dateTime(),

                                        Infolists\Components\TextEntry::make('timetable.timeslot.end_time')
                                            ->label('End time')
                                            ->icon('heroicon-o-clock')
                                            ->dateTime(),

                                        Infolists\Components\TextEntry::make('timetable.room.name')
                                            ->label('Room')
                                            ->icon('heroicon-o-building-office-2')
                                            ->badge(),
                                    ]),
                            ]),
                    ]),
                Infolists\Components\Grid::make(4)
                    ->columnSpan(4)
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('id_pfe')
                            ->label('PFE ID')
                            ->icon('heroicon-o-identification')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('students_names')
                            ->icon('heroicon-o-users')
                            ->badge(),

                        Infolists\Components\TextEntry::make('students_programs')
                            ->icon('heroicon-o-academic-cap')
                            ->badge(),

                        Infolists\Components\TextEntry::make('organization_name')
                            ->icon('heroicon-o-building-office')
                            ->badge()
                            ->color('success'),
                    ]),
                Infolists\Components\Section::make(__('Project Details'))
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->columnSpan(8)
                    ->schema([

                        Infolists\Components\TextEntry::make('title')
                            ->columnSpanFull()
                            ->markdown()
                            ->icon('heroicon-o-document-text')
                            ->formatStateUsing(function ($record) {
                                $comparison = self::compareTitles($record);

                                if ($comparison['matches']) {
                                    return "{$comparison['project_title']}";
                                }

                                return "{$comparison['project_title']}\n\n" .
                                       "<span style='color: red'>**" . __('Agreement title') . ":** {$comparison['agreement_title']}</span>";
                            }),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull()
                            ->markdown()
                            ->icon('heroicon-o-document-text'),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                            ]),
                    ]),
                Infolists\Components\Grid::make(4)
                    ->columnSpan(4)
                    ->schema([

                        Infolists\Components\Section::make(__('Defense status'))
                            ->icon('heroicon-o-academic-cap')
                            ->columns(2)
                            ->columnSpan(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('defense_status')
                                    ->label(false)
                                    ->badge()
                                    ->icon(fn ($state) => $state?->getIcon())
                                    ->color(fn ($state) => $state?->getColor()),

                                Infolists\Components\Grid::make(3)
                                    ->hidden(fn ($record) => $record->defense_status !== Enums\DefenseStatus::Authorized)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('defense_authorized_at')
                                            ->icon('heroicon-o-check-circle')
                                            ->dateTime()
                                            ->color('success'),

                                        Infolists\Components\TextEntry::make('defense_authorized_by_user.name')
                                            ->label('Authorized by')
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('info'),
                                    ]),
                            ]),
                        Infolists\Components\Section::make(__('External Supervisor'))
                            ->columnSpan(4)
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\TextEntry::make('externalSupervisor.full_name')
                                    ->label(fn ($record) => $record->externalSupervisor->full_name)
                                    ->formatStateUsing(fn ($record) => $record->externalSupervisor->email
                                            ? "[{$record->externalSupervisor->email}](mailto:{$record->externalSupervisor->email})"
                                            : null)
                                    ->tooltip(fn ($record) => $record->externalSupervisor->phone
                                            ? __('Phone') . ': ' . $record->externalSupervisor->phone
                                            : null)
                                    ->icon('heroicon-o-envelope')
                                    ->markdown()
                                    ->copyable(fn ($record) => (bool) $record->externalSupervisor->email)
                                    ->copyMessage('Email copied!')
                                    ->copyMessageDuration(1500),
                            ]),
                        Infolists\Components\Section::make(__('Defense documents'))
                            ->icon('heroicon-o-document-text')
                            // ->columnSpan(4)
                            // ->columns(2)
                            ->hidden(fn ($record) => $record->defense_status !== Enums\DefenseStatus::Authorized)
                            ->schema([
                                Infolists\Components\TextEntry::make('evaluation_sheet_url')
                                    ->label('Jury evaluation sheet')
                                    ->icon('heroicon-o-clipboard-document-check')
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state
                                        ? '[' . __('View evaluation sheet') . "]({$state})"
                                        : __('Not generated yet'))
                                    ->markdown()
                                    ->copyable(fn ($state) => (bool) $state)
                                    ->copyMessage('Link copied!')
                                    ->copyMessageDuration(1500),

                                Infolists\Components\TextEntry::make('organization_evaluation_sheet_url')
                                    ->label('Organization evaluation sheet')
                                    ->icon('heroicon-o-building-office')
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state
                                        ? '[' . __('View organization sheet') . "]({$state})"
                                        : __('No file uploaded'))
                                    ->markdown()
                                    ->copyable(fn ($state) => (bool) $state)
                                    ->copyMessage('Link copied!')
                                    ->copyMessageDuration(1500),
                            ]),
                    ]),

            ]);
    }
}
