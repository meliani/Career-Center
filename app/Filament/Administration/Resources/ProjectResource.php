<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\ProjectResource\Pages;
use App\Filament\Administration\Resources\ProjectResource\RelationManagers;
use App\Filament\Core\BaseResource as Resource;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static ?string $title = 'Manage final projects';

    protected static ?string $recordTitleAttribute = 'organization';

    protected static ?string $navigationGroup = 'Students and projects';

    // protected static ?string $navigationParentItem = '';
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $sort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project informations')
                    ->schema([
                        Forms\Components\TextInput::make('id_pfe')
                            ->numeric(),
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
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\ImportProfessorsFromInternshipAgreements::make('Import Professors From Internship Agreements')
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                    // ->hidden(fn ($livewire) => $livewire->ownerRecord->...),

                ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id_pfe')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students.long_full_name')
                    ->label('Student name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internshipAgreements.assigned_department')
                    ->label('Assigned department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('professors.long_full_name')
                    ->label('Supervisor - Reviewer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
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
            // ->filters([
            //     Tables\Filters\SelectFilter::make('internshipAgreements.assigned_department')
            //         ->multiple()
            //         ->relationship('internshipAgreements', 'assigned_department')
            //         ->label('Department'),
            // ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                \Parallax\FilamentComments\Tables\Actions\CommentsAction::make()
                    ->label(__('Comments'))
                    // ->action('comments')
                    ->visible(fn () => true)
                    ->badge(fn ($record) => $record->filamentComments()->count() ?? ''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProfessorsRelationManager::class,
            // RelationManagers\TeammateRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
            // RelationManagers\InternshipAgreementsRelationManager::class,
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
