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

    protected static ?string $recordTitleAttribute = 'title';
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
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->placeholder('Enter a descriptive title')
                    ->helperText('A descriptive title for this schedule configuration')
                    ->maxLength(255),

                Forms\Components\Section::make('Schedule Date Range')
                    ->schema([
                        Forms\Components\DatePicker::make('schedule_starting_at')
                            ->label('Schedule Starting Date')
                            ->required()
                            ->helperText('The first day of the scheduling period'),
                        Forms\Components\DatePicker::make('schedule_ending_at')
                            ->label('Schedule Ending Date')
                            ->required()
                            ->helperText('The last day of the scheduling period')
                            ->after('schedule_starting_at'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Daily Schedule')
                    ->schema([
                        Forms\Components\TimePicker::make('day_starting_at')
                            ->label('Day Start Time')
                            ->required()
                            ->seconds(false)
                            ->helperText('The time when the day schedule starts'),
                        Forms\Components\TimePicker::make('day_ending_at')
                            ->label('Day End Time')
                            ->required()
                            ->seconds(false)
                            ->helperText('The time when the day schedule ends')
                            ->after('day_starting_at'),
                        Forms\Components\TimePicker::make('lunch_starting_at')
                            ->label('Lunch Break Start')
                            ->required()
                            ->seconds(false)
                            ->helperText('The time when lunch break starts'),
                        Forms\Components\TimePicker::make('lunch_ending_at')
                            ->label('Lunch Break End')
                            ->required()
                            ->seconds(false)
                            ->helperText('The time when lunch break ends')
                            ->after('lunch_starting_at'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Slot Duration')
                    ->schema([
                        Forms\Components\TextInput::make('minutes_per_slot')
                            ->label('Minutes per Slot')
                            ->required()
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(180)
                            ->step(15)
                            ->default(90)
                            ->helperText('Duration of each defense slot in minutes'),
                    ]),

                \Filament\Forms\Components\Actions::make([
                    // Actions\Action\Processing\GenerateTimeslotsAction::make('Generate Timeslots')
                    //     ->label(__('Generate Timeslots'))
                    //     ->requiresConfirmation(),
                    Actions\Action\Processing\GenerateTimeslotsFromArtisanAction::make('Generate Timeslots From Artisan')
                        ->label(__('Generate Timeslots'))
                        ->requiresConfirmation()
                        ->slideOver()
                        ->color('success'),
                    // Actions\Action\Processing\GenerateTimetableAction::make('Generate Timetable')
                    //     ->label(__('Generate Defenses Timetable'))
                    //     ->requiresConfirmation(),
                    Actions\Action\Processing\GenerateTimetableFromArtisanAction::make('Generate Timetable From Artisan')
                        ->label(__('Generate Defenses Timetable'))
                        ->requiresConfirmation()
                        // ->form([
                        //     Forms\Components\DatePicker::make('startDate')->required(),
                        //     Forms\Components\DatePicker::make('endDate')->required(),
                        // ])
                        ->slideOver()
                        ->color('success'),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule_starting_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule_ending_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_starting_at'),
                Tables\Columns\TextColumn::make('day_ending_at'),
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
