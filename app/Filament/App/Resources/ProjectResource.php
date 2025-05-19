<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Filament\App\Resources\ProjectResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\Project;
use App\Models\Year;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends StudentBaseResource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'My Project';

    protected static ?string $pluralModelLabel = 'My Project';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'PFE';

    protected static ?int $navigationSort = 4;

    // public static function shouldRegisterNavigation(): bool
    // {
    //     // return auth()->user()->isStudent();
    // }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('agreements', function ($query) {
                $query->whereHas('agreeable', function ($query) {
                    $query->where('student_id', auth()->id())
                        ->where('year_id', Year::current()->id);
                });
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Project Title')
                    ->toggleable(false)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('organization_name')
                    ->label('Organization')
                    ->toggleable(false)
                    ->badge(),
                Tables\Columns\TextColumn::make('defense_status')
                    ->badge()
                    ->color(fn ($state) => $state?->getColor())
                    ->toggleable(false)
                    ->icon(fn ($state) => $state?->getIcon()),
                Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                    ->label('Defense Date')
                    ->dateTime()
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->defaultSort('timetable.timeslot.start_time', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Project Details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Project Title')
                            ->columnSpanFull()
                            ->markdown(),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull()
                            ->markdown(),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->date(),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Defense information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('defense_status')
                            ->badge()
                            ->color(fn ($state) => $state?->getColor()),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('timetable.timeslot.start_time')
                                    ->label('Start Time')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('timetable.timeslot.end_time')
                                    ->label('End Time')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('timetable.room.name')
                                    ->label('Room')
                                    ->badge(),
                            ]),
                    ])
                    ->visible(false),

                Infolists\Components\Section::make(__('Supervisors'))
                    ->schema([
                        Infolists\Components\TextEntry::make('academic_supervisor_name')
                            ->label('Academic Supervisor'),
                        Infolists\Components\TextEntry::make('externalSupervisor.full_name')
                            ->label('External Supervisor'),
                    ]),

                Infolists\Components\Section::make('Documents')
                    ->visible(fn ($record) => $record->defense_status === Enums\DefenseStatus::Authorized)
                    ->schema([
                        Infolists\Components\TextEntry::make('evaluation_sheet_url')
                            ->label('Evaluation Sheet')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab()
                            ->visible(fn ($state) => $state !== null),
                    ]),
            ]);
    }
}
