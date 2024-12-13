<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Project;
use App\Models\Year;
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
use Guava\FilamentModalRelationManagers\Actions\Infolist\RelationManagerAction as InfolistRelationManagerAction;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Hydrat\TableLayoutToggle\Facades\TableLayoutToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel;

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
        return static::getModel()::active()->count();
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'agreements.agreeable.organization.name',
            'agreements.agreeable.student.first_name',
            'agreements.agreeable.student.last_name',
            'agreements.agreeable.student.id_pfe',
            'professors.first_name',
            'professors.last_name',
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

    // public static function getEloquentQuery(): Builder
    // {
    //     // return parent::getEloquentQuery()
    //     //     ->whereHas('internship_agreements', function ($query) {
    //     //         $query->whereHas('year', function ($q) {
    //     //             $q->where('id', Year::current()->id);
    //     //         });
    //     //     });
    // }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['agreements.agreeable.student']);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ProfessorsRelationManager::class,
            // RelationGroup::make(__('Students'), [
            //     // RelationManagers\StudentsRelationManager::class,
            //     RelationManagers\InternshipAgreementsRelationManager::class,
            //     // RelationManagers\CommentsRelationManager::class,
            // ]),
            // RelationGroup::make(__('Defense Details'), [
            //     RelationManagers\TimetableRelationManager::class,
            // ]),
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
                    ])
                    ->collapsible()
                    ->columns(2),
                Forms\Components\Section::make('Defense information')
                    ->label(__('Defense information'))
                    ->columnSpan(1)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->relationship('timetable')
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
                            ->required()
                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                        Forms\Components\Select::make('room_id')
                            ->relationship('room', 'name')
                            ->required()
                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                    ])
                    ->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->defaultPaginationPageOption(10)
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            // ->striped()
            // ->deferLoading()
            ->defaultSort('timetable.timeslot.start_time')
            ->defaultGroup('timetable.timeslot.start_time')
            ->groups([
                Tables\Grouping\Group::make('timetable.timeslot.start_time')
                    ->date()
                    ->collapsible()
                    ->label(__('Day of')),
                Tables\Grouping\Group::make('defense_status')
                    ->collapsible()
                    ->label(__('Defense status')),
            ])
            ->columns(
                $livewire->isGridLayout()
                    ? \App\Services\Filament\Tables\Projects\ProjectsGrid::get()
                    : \App\Services\Filament\Tables\Projects\ProjectsTable::get()
            )
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
                SelectFilter::make('agreement_type')
                    ->label('Internship Type')
                    ->options([
                        'internship' => 'Introductory/Technical Internship',
                        'final_year' => 'Final Year Internship',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, string $value): Builder {
                                return $query->whereHas('agreements', function (Builder $query) use ($value) {
                                    if ($value === 'internship') {
                                        $query->where('agreeable_type', InternshipAgreement::class);
                                    } elseif ($value === 'final_year') {
                                        $query->where('agreeable_type', FinalYearInternshipAgreement::class);
                                    }
                                });
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['value']) {
                            return null;
                        }

                        return 'Type: ' . (
                            $data['value'] === 'internship'
                            ? 'Introductory/Technical'
                            : 'Final Year'
                        );
                    })
                    ->columnSpanFull(), // Makes the filter span the full width

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
                Tables\Filters\Filter::make('Defense date')
                    ->form([
                        Forms\Components\DatePicker::make('defenses_from'),
                        // ->default(now()),
                        Forms\Components\DatePicker::make('defenses_until'),
                        // ->default(now()->addDays(7)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['defenses_from'],
                                fn (Builder $query, $date): Builder => $query->whereRelation('timetable.timeslot', 'start_time', '>=', $date),
                            )
                            ->when(
                                $data['defenses_until'],
                                fn (Builder $query, $date): Builder => $query->whereRelation('timetable.timeslot', 'start_time', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['defenses_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(__('Defenses from :date', ['date' => Carbon::parse($data['defenses_from'])->toFormattedDateString()]))
                                ->removeField('defenses_from');
                        }

                        if ($data['defenses_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(__('Defenses until :date', ['date' => Carbon::parse($data['defenses_until'])->toFormattedDateString()]))
                                ->removeField('defenses_until');
                        }

                        return $indicators;
                    }),
                // ->default(),

                Tables\Filters\SelectFilter::make('defense_status')
                    ->options(Enums\DefenseStatus::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $status): Builder => $query->where('defense_status', $status)
                        ),
                    ),
                Tables\Filters\SelectFilter::make('professor')
                    ->searchable()
                    ->preload()
                    ->relationship('professors', 'name'),
                Tables\Filters\SelectFilter::make('Assigned department')
                    ->options(Enums\Department::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $department): Builder => $query->whereRelation('internship_agreements', 'assigned_department', $department)
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
                    ->default(Year::current()->id)
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
                    ->indicateUsing(fn (array $data): ?string => $data['value'] ? __('Year') . ': ' . Year::find($data['value'])->title : null)
                    ->columnSpanFull(),
            ])
            ->headerActions([
                /*                 Tables\Actions\Action::make('Check changed professors')
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
                                'timetable.timeslot.start_time',
                                'timetable.timeslot.end_time',
                                'start_date',
                                'end_date',
                            ])
                            ->withColumns([
                                FilamentExcel\Columns\Column::make('title')->width(10),
                                FilamentExcel\Columns\Column::make('description')->width(10),
                                FilamentExcel\Columns\Column::make('organization')->width(10),
                                FilamentExcel\Columns\Column::make('keywords')->width(6),

                            ]),
                    ]),
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
                    Tables\Actions\Action::make('Postpone')
                        ->label('Postpone defense')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->hidden(fn ($record) => auth()->user()->can('authorize-defense', $record) === false)
                        ->action(fn ($record) => $record->postponeDefense()),

                    // Tables\Actions\ActionGroup::make([
                    // Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])->icon('heroicon-o-bars-3')
                    ->hidden(fn ($record) => auth()->user()->can('manage-project', $record) === false),
                // ])->dropdown(false)
                // ->tooltip(__('Edit or view this project')),
                // ->hidden(true),
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
            ]);

    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Infolists\Components\Section::make(__('Project Details'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Infolists\Components\Grid::make(3)
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

                        Infolists\Components\TextEntry::make('title')
                            ->columnSpanFull()
                            ->markdown()
                            ->icon('heroicon-o-document-text'),
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

                Infolists\Components\Section::make(__('Defense status'))
                    ->icon('heroicon-o-academic-cap')
                    ->columns(3)
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

                Infolists\Components\Tabs::make('Relations')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Jury Members'))
                            ->icon('heroicon-o-users')
                            ->schema([
                                Infolists\Components\Section::make(__('Jury Members'))
                                    ->icon('heroicon-o-users')
                                    ->columns(3)
                                    ->headerActions([
                                        InfolistRelationManagerAction::make('professors-relation-manager')
                                            ->label(__('Edit jury members'))
                                            ->relationManager(RelationManagers\ProfessorsRelationManager::class)
                                            ->hidden(fn (Request $request, $record) => (auth()->user()->can('manage-supervision', $record)) === false)
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false),
                                    ])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('academic_supervisor')
                                            ->label(__('Academic supervisor'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->badge()
                                            ->color('info'),

                                        Infolists\Components\TextEntry::make('reviewer1')
                                            ->label(__('First Reviewer'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('success'),

                                        Infolists\Components\TextEntry::make('reviewer2')
                                            ->label(__('Second Reviewer'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('success'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Schedule'))
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Infolists\Components\Section::make(__('Defense Schedule'))
                                    ->icon('heroicon-o-calendar')
                                    ->columns(3)
                                    ->headerActions([
                                        InfolistRelationManagerAction::make('timetable-relation-manager')
                                            ->label(__('Edit timetable'))
                                            ->relationManager(RelationManagers\TimetableRelationManager::class)
                                            ->hidden(fn (Request $request, $record) => (auth()->user()->can('manage-project', [
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
                    ])
                    ->columnSpanFull(),

                Infolists\Components\Section::make(__('External Supervisor'))
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
                    ->columnSpanFull()
                    ->columns(2)
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
            ]);
    }
}
