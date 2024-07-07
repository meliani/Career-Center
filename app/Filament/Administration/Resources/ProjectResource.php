<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Hydrat\TableLayoutToggle\Facades\TableLayoutToggle;
use Illuminate\Contracts\Database\Query\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Parallax\FilamentComments\Actions\CommentsAction;
use Parallax\FilamentComments\Infolists\Components\CommentsEntry;
use pxlrbt\FilamentExcel;

class ProjectResource extends Core\BaseResource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Final project defense';

    protected static ?string $pluralModelLabel = 'Final projects defenses';

    protected static ?string $title = 'title';

    protected static ?string $recordTitleAttribute = 'students_names';

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'Students and projects';

    protected static ?int $sort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'internship_agreements.organization_name',
            'students.first_name',
            'students.last_name',
            'internship_agreements.id_pfe',
            'professors.first_name',
            'professors.last_name',
        ];
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor();
        }

        return false;
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
                    ->label('Defense information')
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
            ->deferLoading()
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
                        Forms\Components\DatePicker::make('defenses_from')
                            ->default(now()),
                        Forms\Components\DatePicker::make('defenses_until')
                            ->default(now()->addDays(7)),
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
                    })
                    ->default(),

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

                Tables\Filters\SelectFilter::make('hasTeammate')
                    ->label('Has teammate')
                    ->options([
                        'yes' => __('With teammate'),
                        'no' => __('Without teammate'),
                    ])
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->whereHas('students', fn ($query) => $query->select('project_id')->groupBy('project_id')->havingRaw('COUNT(*) > 1'))
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->whereDoesntHave('students', fn ($query) => $query->select('project_id')->groupBy('project_id')->havingRaw('COUNT(*) <= 1'))
                        ),
                    ),

            ])
            ->headerActions([
                \App\Filament\Actions\Action\Processing\GoogleSheetSyncAction::make('Google Sheet Sync')
                    ->label('Google Sheet Sync')
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),

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
                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\Action\Processing\ImportProfessorsFromInternshipAgreements::make('Import Professors From Internship Agreements')
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                ]),

            ])
            ->actions([
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
                    \Parallax\FilamentComments\Tables\Actions\CommentsAction::make()
                        ->label('Comment')
                        ->tooltip(
                            fn ($record) => "{$record->filamentComments()->count()} " . __('Comments')
                        )
                        // ->visible(fn () => true)
                        ->badge(fn ($record) => $record->filamentComments()->count() ? $record->filamentComments()->count() : null),

                    // Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])
                    ->icon('heroicon-o-bars-3'),
                // ])->dropdown(false)
                // ->tooltip(__('Edit or view this project')),
                // ->hidden(true),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendConnectingSupervisorsEmail::make('SendConnectingSupervisorsEmail')
                        ->label(__('Connection email : Supervisors / Students'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->requiresConfirmation()
                        ->outlined(),
                ])->label(__('Mass Notification Emails'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Large)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('authorize')
                        ->label('Authorize')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->authorizeDefense()),
                    Tables\Actions\BulkAction::make('postpone')
                        ->label('Postpone')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->postponeDefense()),
                    Tables\Actions\BulkAction::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->completeDefense()),
                    Tables\Actions\BulkAction::make('markAllProfessorsAsPresent')
                        ->label('Mark jury as present')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(auth()->user()->cannot('manage-projects'))
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->markAllProfessorsAsPresent()),
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                ])
                    ->label(__('Mass prossessing'))
                    ->hidden(fn () => auth()->user()->cannot('manage-projects')),
                BulkAction\Email\SendGenericEmail::make('Send mass emails to students')
                    ->tooltip(__('Send customized generic mass emails to students'))
                    ->label(__('Write mass emails to students'))
                    ->outlined(),

            ]);
        // ;

    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProfessorsRelationManager::class,
            RelationGroup::make(__('Students and Internship Agreements'), [
                RelationManagers\StudentsRelationManager::class,
                RelationManagers\InternshipAgreementsRelationManager::class,
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

    public static function infolist(Infolist $infolist): Infolist
    {
        $organization_evaluation_sheet_url = $infolist->getRecord()->organization_evaluation_sheet_url ? url($infolist->getRecord()->organization_evaluation_sheet_url) : null;
        $evaluation_sheet_url = $infolist->getRecord()->evaluation_sheet_url ? url($infolist->getRecord()->evaluation_sheet_url) : null;

        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Defense documents'))
                    // ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('evaluation_sheet_url')
                            ->label('Jury evaluation sheet')
                            ->formatStateUsing(
                                fn ($record) => $record->evaluation_sheet_url ? '[' . __('Preview file') . "]({$record->evaluation_sheet_url})" : null
                            )
                            ->tooltip(
                                fn ($record) => $record->evaluation_sheet_url ? __('To open the file, right click -> open in a new tab') : null
                            )
                            ->simpleLightbox($evaluation_sheet_url)
                            ->markdown(),
                        Infolists\Components\TextEntry::make('organization_evaluation_sheet_url')
                            ->label('Organization evaluation sheet')
                            ->formatStateUsing(
                                fn ($record) => $record->organization_evaluation_sheet_url ? '[' . __('Preview file') . "]({$record->organization_evaluation_sheet_url})" : null
                            )
                            ->tooltip(
                                fn ($record) => $record->organization_evaluation_sheet_url ? __('To open the file, right click -> open in a new tab') : null
                            )
                            ->simpleLightbox($organization_evaluation_sheet_url)
                            ->markdown(),
                    ]),

                Infolists\Components\Section::make(__('Defense information'))
                    // ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('defense_status')
                            ->label('Defense status'),
                        Infolists\Components\TextEntry::make('defense_authorized_at')
                            ->label('Defense authorized at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('defense_authorized_by_user.name')
                            ->label('Defense authorized by'),
                        Infolists\Components\TextEntry::make('timetable.timeslot.start_time')
                            ->label('Defense start time')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('timetable.timeslot.end_time')
                            ->label('Defense end time')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('timetable.room.name')
                            ->label('Room'),
                        Infolists\Components\Fieldset::make('Jury')
                            ->columns(3)
                            ->schema([
                                // RepeatableEntry::make('supervisor')
                                //     ->label('')
                                //     ->schema([
                                //         Infolists\Components\TextEntry::make('name')
                                //             ->label(__('Supervisor')),
                                //     ])
                                //     ->contained(false),
                                // RepeatableEntry::make('reviewers')
                                //     ->label('')
                                //     ->schema([
                                //         Infolists\Components\TextEntry::make('name')
                                //             ->label(__('Reviewer')),
                                //     ])
                                //     ->contained(false),
                                // Infolists\Components\TextEntry::make('professors.name')
                                //     ->label('')
                                //     ->formatStateUsing(
                                //         fn ($record) => $record->professors->map(
                                //             fn ($professor) => $professor->pivot->jury_role->getLabel() . ': ' . $professor->name
                                //         )->join(', ')
                                //     ),

                                Infolists\Components\TextEntry::make('academic_supervisor')
                                    ->label('Academic supervisor'),
                                Infolists\Components\TextEntry::make('reviewer1')
                                    ->label('Reviewer 1'),
                                Infolists\Components\TextEntry::make('reviewer2')
                                    ->label('Reviewer 2'),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Project information'))
                    ->headerActions([
                        // CommentsAction::make(),
                    ])
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('id_pfe')
                            ->label('PFE ID'),
                        Infolists\Components\TextEntry::make('students.long_full_name')
                            ->label('Student'),
                        Infolists\Components\TextEntry::make('students.program')
                            ->label('Program'),
                        Infolists\Components\TextEntry::make('organization')
                            ->label('Organization'),

                        Infolists\Components\TextEntry::make('title')
                            ->label('Project title'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Project description')
                            ->formatStateUsing(
                                fn ($record) => "{$record->description}"
                            )
                            ->html()
                            ->columnSpanFull(),
                        Infolists\Components\Fieldset::make('Dates')
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('Project start date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->label('Project end date')
                                    ->date(),
                            ]),

                        Infolists\Components\Fieldset::make('Entreprise supervisor')
                            ->schema([
                                Infolists\Components\TextEntry::make('internship_agreements.encadrant_ext_nom')
                                    ->label('')
                                    ->formatStateUsing(
                                        fn ($record) => $record->internship_agreements->map(
                                            fn ($internship_agreement) => "**{$internship_agreement->encadrant_ext_name}**" . PHP_EOL . $internship_agreement->encadrant_ext_mail . PHP_EOL . $internship_agreement->encadrant_ext_tel
                                        )->join(', ')
                                    )
                                    ->markdown(),

                            ]),
                        // CommentsEntry::make('Comments')
                        //     ->columnSpanFull(),
                    ]),

            ]);

    }
}
