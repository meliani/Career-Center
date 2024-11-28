<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\FinalProjectResource\Pages;
use App\Filament\Administration\Resources\FinalProjectResource\RelationManagers;
use App\Models\FinalProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinalProjectResource extends Resource
{
    protected static ?string $model = FinalProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project information')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\MarkdownEditor::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnspan(2),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false),
                        Forms\Components\Select::make('language')
                            ->options([
                                'en' => 'English',
                                'fr' => 'French',
                            ]),
                        Forms\Components\Select::make('organization_id')
                            ->preload()
                            ->relationship(
                                name: 'organization',
                                titleAttribute: 'name'
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record) => "{$record->name} - {$record->city}"
                            )
                            ->searchable(['name', 'city']),
                        Forms\Components\Select::make('external_supervisor_id')
                            ->preload()
                            ->relationship(
                                name: 'externalSupervisor',
                                titleAttribute: 'full_name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record) => "{$record->full_name} - {$record->function}"
                            )
                            ->searchable(['first_name', 'last_name']),
                    ])
                    ->collapsible()
                    ->columns(2),

                // Forms\Components\Section::make('Defense information')
                //     ->columnSpan(1)
                //     ->relationship('timetables')
                //     ->schema([
                //         Forms\Components\Select::make('timeslot_id')
                //             ->relationship('timeslot', 'start_time')
                //             ->required(),
                //         Forms\Components\Select::make('room_id')
                //             ->relationship('room', 'name')
                //             ->required(),
                //     ])
                //     ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(10)
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->deferLoading()
            ->defaultSort('timetable.timeslot.start_time')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('students.name')
                    ->label('Students')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_agreement.organization.name')
                    ->label('Organization')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_agreement.external_supervisor.full_name')
                    ->label('External Supervisor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('defense_status')
                    ->badge(),
                Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                    ->label('Defense Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timetable.room.name')
                    ->label('Room'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Defense date')
                    ->form([
                        Forms\Components\DatePicker::make('defenses_from'),
                        Forms\Components\DatePicker::make('defenses_until'),
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
                    }),
                Tables\Filters\SelectFilter::make('defense_status')
                    ->options(Enums\DefenseStatus::class),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
            RelationManagers\ProfessorsRelationManager::class,
            RelationManagers\TimetablesRelationManager::class,
            RelationManagers\FinalInternshipAgreementRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinalProjects::route('/'),
            'create' => Pages\CreateFinalProject::route('/create'),
            'view' => Pages\ViewFinalProject::route('/{record}'),
            'edit' => Pages\EditFinalProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
