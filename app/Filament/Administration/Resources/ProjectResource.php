<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core;
use App\Models\Project;
use Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title', 'organization',
            'students.first_name',
            'students.last_name',
            'id_pfe', 'professors.name',
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
                        Forms\Components\TextInput::make('id_pfe')
                            ->maxLength(10),
                        Forms\Components\Textarea::make('title')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('organization')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('end_date'),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(20)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('internship_agreements.id_pfe')
                    ->label('ID PFE')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('students.full_name')
                    ->label('Student name')
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    ->sortableMany(),
                Tables\Columns\TextColumn::make('students.program')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internship_agreements.assigned_department')
                    ->label('Assigned department')
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

                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->sortable(),
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

                Tables\Filters\SelectFilter::make('department')
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
                        'yes' => 'Yes',
                        'no' => 'No',
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
                        ->label(__('Send email to put supervisors in contact with students'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->requiresConfirmation()
                        ->outlined(),
                ])
                    ->label(__('Send email'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Large)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label(__('actions'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                BulkAction\Email\SendGenericEmail::make('Send Email')
                    ->outlined(),

            ]);
        // ;

    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProfessorsRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
            RelationManagers\InternshipAgreementsRelationManager::class,
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
                Infolists\Components\Section::make(__('Internship agreement and validation process'))
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('edit page', 'edit')
                            ->label('Edit')
                            ->icon('heroicon-o-pencil')
                            ->size(Filament\Support\Enums\ActionSize::Small)
                            ->tooltip('Edit this internship agreement')
                            ->url(fn ($record) => \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$record->id])),

                    ])
                    ->schema([

                        Infolists\Components\Fieldset::make('Internship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Project title'),
                                Infolists\Components\TextEntry::make('organization')
                                    ->label('Organization'),
                                Infolists\Components\TextEntry::make('id_pfe')
                                    ->label('PFE ID'),
                                Infolists\Components\TextEntry::make('students.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('professors.name')
                                    ->label('Supervisor - Reviewer')
                                    ->formatStateUsing(
                                        fn ($record) => $record->professors->map(
                                            fn ($professor) => $professor->pivot->jury_role->getLabel() . ': ' . $professor->name
                                        )->join(', ')
                                    ),
                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('Project start date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('end_date')

                                    ->label('Project end date')
                                    ->date(),
                            ]),
                    ]),
            ]);
    }
}
