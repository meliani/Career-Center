<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Actions\Email;
use App\Filament\Administration\Resources\ProjectResource\Pages;
// use App\Filament\Exports\ProjectExporter;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core\BaseResource as Resource;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static ?string $title = 'Manage final projects';

    protected static ?string $recordTitleAttribute = 'organization';

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $sort = 4;

    public static function getnavigationGroup(): string
    {
        return __('Students and projects');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'organization', 'students.full_name', 'id_pfe', 'professors.name'];
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
            ->headerActions([
                // Tables\Actions\AttachAction::make(),

                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\ImportProfessorsFromInternshipAgreements::make('Import Professors From Internship Agreements')
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                ]),
                // ExportAction::make()
                //     ->exporter(ProjectExporter::class),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
                // ->exports([
                //     \pxlrbt\FilamentExcel\Exports\ExcelExport::make()->withColumns([
                //         \pxlrbt\FilamentExcel\Columns\Column::make('professors')
                //         // i want to get professor with
                //             ->formatStateUsing(fn ($record) => $record->professors->map(fn ($professor) => __($professor->pivot->jury_role).' : '.$professor->name)->join(', ')),
                //     ]),
                // ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id_pfe')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('students.full_name')
                    ->label('Student name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internshipAgreements.assigned_department')
                    ->label('Assigned department')
                    ->tooltip(fn ($state) => implode(', ', array_map(function ($s) {
                        return $s->getDescription();
                    }, $state)))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('professors.name')
                    ->label('Supervisor - Reviewer')
                    ->formatStateUsing(
                        fn ($record) => $record->professors->map(
                            fn ($professor) => $professor->pivot->jury_role->getLabel() . ': ' . $professor->name
                        )->join(', ')
                    )
                    // ->listWithLineBreaks()
                    // ->bulleted()
                    ->searchable()
                    // ->formatStateUsing(function ($state, Project $project) {
                    //     return $project->professors->map(function ($professor) {
                    //         return $professor->name;
                    //     })->join(', ');
                    // })
                    ->sortable(),
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
            ->actions([
                \Parallax\FilamentComments\Tables\Actions\CommentsAction::make()
                    ->label(__('Comments'))
                    // ->visible(fn () => true)
                    ->badge(fn ($record) => $record->filamentComments()->count() ?? ''),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
                // ->hidden(true),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Email\SendInternshipKickoffEmail::make('Send Internship Kickoff Email'),
                ]),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
