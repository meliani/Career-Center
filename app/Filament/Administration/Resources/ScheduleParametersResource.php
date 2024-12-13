<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Actions;
use App\Filament\Administration\Resources\ScheduleParametersResource\Pages;
use App\Filament\Core\BaseResource as Resource;
use App\Models\ScheduleParameters;
use Filament\Forms;
// use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleParametersResource extends Resource
{
    protected static ?string $model = ScheduleParameters::class;

    protected static ?string $title = 'Schedules parameters';

    protected static ?string $modelLabel = 'Schedule parameters';

    protected static ?string $pluralModelLabel = 'Schedules parameters';

    // protected static ?string $recordTitleAttribute = 'schedule_starting_at';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $sort = 8;

    public $starting_from;

    public $ending_at;

    public $working_from;

    public $working_to;

    public $number_of_rooms;

    public $max_rooms;

    public $minutes_per_slot;

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('schedule_starting_at')
                    ->required(),
                Forms\Components\DatePicker::make('schedule_ending_at')
                    ->required(),
                Forms\Components\TimePicker::make('day_starting_at')
                    ->required(),
                Forms\Components\TimePicker::make('day_ending_at')
                    ->required(),
                Forms\Components\TimePicker::make('lunch_starting_at')
                    ->required(),
                Forms\Components\TimePicker::make('lunch_ending_at')
                    ->required(),
                Forms\Components\TextInput::make('minutes_per_slot')
                    ->required()
                    ->numeric(),

                \Filament\Forms\Components\Actions::make([
                    Actions\Action\Processing\GenerateTimeslotsAction::make('Generate Timeslots')
                        ->label(__('Generate Timeslots'))
                        ->requiresConfirmation(),
                    Actions\Action\Processing\GenerateTimeslotsFromArtisanAction::make('Generate Timeslots From Artisan')
                        ->label(__('Generate Timeslots From Artisan'))
                        ->requiresConfirmation()
                        ->slideOver(),
                    Actions\Action\Processing\GenerateTimetableAction::make('Generate Timetable')
                        ->label(__('Generate Defenses Timetable'))
                        ->requiresConfirmation(),
                    Actions\Action\Processing\GenerateTimetableFromArtisanAction::make('Generate Timetable From Artisan')
                        ->label(__('Generate Defenses Timetable From Artisan'))
                        ->requiresConfirmation()
                        // ->form([
                        //     Forms\Components\DatePicker::make('startDate')->required(),
                        //     Forms\Components\DatePicker::make('endDate')->required(),
                        // ])
                        ->slideOver(),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schedule_starting_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule_ending_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_starting_at'),
                Tables\Columns\TextColumn::make('day_ending_at'),
                Tables\Columns\TextColumn::make('max_defenses_per_professor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minutes_per_slot')
                    ->numeric()
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
            'index' => Pages\ListScheduleParameters::route('/'),
            'create' => Pages\CreateScheduleParameters::route('/create'),
            'edit' => Pages\EditScheduleParameters::route('/{record}/edit'),
        ];
    }
}
