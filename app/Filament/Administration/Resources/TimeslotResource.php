<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\TimeslotResource\Pages;
use App\Models\Timeslot;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Core\BaseResource as Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimeslotResource extends Resource
{
    protected static ?string $model = Timeslot::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getnavigationGroup(): string
    {
        return __('Planning');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('start_time'),
                Forms\Components\DateTimePicker::make('end_time'),
                Forms\Components\TextInput::make('is_enabled')
                    ->maxLength(1),
                Forms\Components\TextInput::make('is_taken')
                    ->maxLength(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_enabled'),
                Tables\Columns\TextColumn::make('is_taken'),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTimeslots::route('/'),
        ];
    }
}
