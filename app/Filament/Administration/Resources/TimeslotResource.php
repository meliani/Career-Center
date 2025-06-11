<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\TimeslotResource\Pages;
use App\Filament\Core\BaseResource as Resource;
use App\Models\Timeslot;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class TimeslotResource extends Resource
{
    protected static ?string $model = Timeslot::class;

    protected static ?string $modelLabel = 'Timeslot';

    protected static ?string $pluralModelLabel = 'Timeslots';

    protected static ?string $title = 'Manage Timeslots';

    // protected static ?string $recordTitleAttribute = '';
    // protected static ?string $navigationParentItem = 'Defenses Timetable';

    protected static ?string $navigationGroup = 'Defense Management';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 800;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('year_id')
                    ->label('Academic Year')
                    ->relationship('year', 'title')
                    ->default(Year::current()?->id)
                    ->required(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->required(),
                Forms\Components\Toggle::make('is_enabled')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year.title')
                    ->label('Academic Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Enabled')
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
                Tables\Filters\SelectFilter::make('year_id')
                    ->label('Academic Year')
                    ->relationship('year', 'title')
                    ->default(Year::current()?->id),
            ])
            ->defaultSort('start_time', 'asc')
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
