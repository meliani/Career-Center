<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Actions\CommentsAction;
use Parallax\FilamentComments\Infolists\Components\CommentsEntry;

class ProjectResource extends Core\BaseResource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static ?string $title = 'Manage final projects';

    protected static ?string $recordTitleAttribute = 'organization';

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

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Project informations')
                    ->schema([
                        Forms\Components\Textarea::make('title')
                            ->disabled(fn () => auth()->user()->isAdministrator() === false)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('start_date')
                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                        Forms\Components\DatePicker::make('end_date')
                            ->disabled(fn () => auth()->user()->isAdministrator() === false),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(10)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('internship_agreements.id_pfe')
                    ->label('ID PFE')
                    ->sortable()
                    ->sortableMany()
                    ->searchable(),
                Tables\Columns\TextColumn::make('students.full_name')
                    ->label('Student name')
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    ->sortableMany(),
                Tables\Columns\TextColumn::make('students.program')
                    ->label('Program')
                    ->searchable()->sortableMany(),
                Tables\Columns\TextColumn::make('internship_agreements.assigned_department')
                    ->label('Assigned department')
                    // ->sortable(false)
                    ->sortableMany()
                    ->searchable(),
                Tables\Columns\TextColumn::make('professors.name')
                    ->label('Supervisor')
                    // ->formatStateUsing(
                    //     fn ($record) => $record->professors->map(
                    //         fn ($professor) =>
                    //         // $professor->pivot->jury_role->getLabel() . ': '
                    //         // .
                    //         $professor->name
                    //     )->join(', ')
                    // )

                    // ->listWithLineBreaks()
                    // ->bulleted()
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    // ->formatStateUsing(function ($state, Project $project) {
                    //     return $project->professors->map(function ($professor) {
                    //         return $professor->name;
                    //     })->join(', ');
                    // })
                    ->sortableMany(),
                Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                    ->label('Defense start time')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state, $record) => $record->timetable->timeslot->start_time->format('Y-m-d H:i:s')
                    )
                    ->dateTime(),
                Tables\Columns\TextColumn::make('timetable.timeslot.end_time')
                    ->label('Defense end time')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state) => date('Y-m-d H:i:s', strtotime($state))
                    )
                    ->dateTime(),

                Tables\Columns\TextColumn::make('internship_agreements.organization_name')
                    ->label('Organization')
                    ->searchable()
                    ->sortableMany(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Project start date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Project end date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->limit(90)
                    ->searchable()
                    ->sortable(),
                /* parrain_fonction
                    parrain_tel
                    parrain_mail
                    encadrant_ext_fonction
                    encadrant_ext_tel
                    encadrant_ext_mail */
                // group of columns

                Tables\Columns\ColumnGroup::make(__('Entreprise Contacts'))
                    ->columns([
                        Tables\Columns\TextColumn::make('internship_agreements.parrain_nom')
                            ->label('Nom Parrain')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.parrain_prenom')
                            ->label('Prenom Parrain')
                            ->searchable()
                            ->sortable(),

                        Tables\Columns\TextColumn::make('internship_agreements.parrain_fonction')
                            ->label('Fonction Parrain')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.parrain_tel')
                            ->label('Tel Parrain')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.parrain_mail')
                            ->label('Mail Parrain')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.encadrant_ext_nom')
                            ->label('Nom Encadrant Externe')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.encadrant_ext_prenom')
                            ->label('Prenom Encadrant Externe')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.encadrant_ext_fonction')
                            ->label('Fonction Encadrant Externe')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.encadrant_ext_tel')
                            ->label('Tel Encadrant Externe')
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('internship_agreements.encadrant_ext_mail')
                            ->label('Mail Encadrant Externe')
                            ->searchable()
                            ->sortable(),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('internship_agreements.assigned_department')
                //     // ->relationship('internship_agreements', 'assigned_department')
                //     ->options([
                //         'MIR' => 'MIR',
                //     ]),

                // Tables\Filters\SelectFilter::make('internship_agreements.assigned_department')
                //     ->label('Assigned Department')
                //     ->options(Enums\Department::class),

                Tables\Filters\SelectFilter::make('Assigned department')
                    ->options(Enums\Department::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->whereHas('internship_agreements', fn ($query) => $query->where('assigned_department', $data['value']))
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
                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\Action\Processing\ImportProfessorsFromInternshipAgreements::make('Import Professors From Internship Agreements')
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                ]),

                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ])
            ->actions([
                \Parallax\FilamentComments\Tables\Actions\CommentsAction::make()
                    ->label('')
                    ->tooltip(
                        fn ($record) => "{$record->filamentComments()->count()} " . __('Comments')
                    )

                    // ->visible(fn () => true)
                    ->badge(fn ($record) => $record->filamentComments()->count() ? $record->filamentComments()->count() : null),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])
                    ->tooltip(__('Edit or view this project')),
                // ->hidden(true),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendConnectingSupervisorsEmail::make('SendConnectingSupervisorsEmail')
                        ->label(__('Connection email : Supervisors / Students'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->requiresConfirmation()
                        ->outlined(),
                ])
                    ->label(__('Mass Notification Emails'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Large)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label(__('edition'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
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
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Project informations'))
                    ->headerActions([
                        // CommentsAction::make(),
                    ])

                    ->columns(3)
                    ->schema([
                        Infolists\Components\Fieldset::make('Project informations')
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

                                Infolists\Components\Fieldset::make('Jury informations')
                                    ->schema([
                                        RepeatableEntry::make('professors')
                                            ->label('')
                                            ->schema([
                                                Infolists\Components\TextEntry::make('name')
                                                    ->label(__('Supervisor')),
                                            ])
                                            ->contained(false),
                                        // Infolists\Components\TextEntry::make('professors.name')
                                        //     ->label('')
                                        //     ->formatStateUsing(
                                        //         fn ($record) => $record->professors->map(
                                        //             fn ($professor) => $professor->pivot->jury_role->getLabel() . ': ' . $professor->name
                                        //         )->join(', ')
                                        //     ),
                                    ]),

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
