<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\TimetableResource\Pages;
use App\Models\Timetable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 10;

    public static function getnavigationGroup(): string
    {
        return __('Planning');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('timeslot_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('room_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('project_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('confirmed_by')
                    ->numeric(),
                Forms\Components\TextInput::make('cancelled_by')
                    ->numeric(),
                Forms\Components\TextInput::make('rescheduled_by')
                    ->numeric(),
                Forms\Components\TextInput::make('deleted_by')
                    ->numeric(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_enabled')
                    ->required(),
                Forms\Components\Toggle::make('is_taken')
                    ->required(),
                Forms\Components\Toggle::make('is_confirmed')
                    ->required(),
                Forms\Components\Toggle::make('is_cancelled')
                    ->required(),
                Forms\Components\Toggle::make('is_rescheduled')
                    ->required(),
                Forms\Components\Toggle::make('is_deleted')
                    ->required(),
                Forms\Components\DateTimePicker::make('confirmed_at'),
                Forms\Components\DateTimePicker::make('cancelled_at'),
                Forms\Components\DateTimePicker::make('rescheduled_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('timeslot.start_time')
                    ->label('Start Time')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timeslot.end_time')
                    ->label('End Time')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('project.professors.name')
                    ->label('Supervisor - Reviewer'),
                Tables\Columns\TextColumn::make('timeslot_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('confirmed_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cancelled_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rescheduled_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_taken')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_cancelled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_rescheduled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_deleted')
                    ->boolean(),
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cancelled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rescheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'edit' => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }
}
