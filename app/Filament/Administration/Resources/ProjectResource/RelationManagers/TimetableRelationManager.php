<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimetableRelationManager extends RelationManager
{
    use \Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

    protected static string $relationship = 'timetable';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('timeslot_id')
                    ->relationship('timeslot', 'start_time', fn ($query) => $query->active())

                    ->required(),
                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(false)
            // ->recordTitleAttribute('name')
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Arrange a schedule'),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->columns([
                Tables\Columns\TextColumn::make('timeslot.start_time')
                    ->dateTime()
                    ->toggleable(false),
                Tables\Columns\TextColumn::make('timeslot.end_time')
                    ->dateTime()
                    ->toggleable(false),
                Tables\Columns\TextColumn::make('room.name')
                    ->toggleable(false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make()
                //     ->label('Schedule'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(false),
                Tables\Actions\DeleteAction::make()
                    ->label('Unschedule')
                    ->icon('heroicon-o-clock'),
            ])
            ->bulkActions([
            ]);
    }
}
