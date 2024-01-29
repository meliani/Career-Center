<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\ScheduleParametersResource\Pages;
use App\Filament\Administration\Resources\ScheduleParametersResource\RelationManagers;
use App\Models\ScheduleParameters;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
// use Filament\Actions\Action;
use App\Filament\Actions\AssignDepartmentAction;
use App\Filament\Actions\ScheduleHeadOfJury;
use App\Filament\Actions\AssignInternshipsToProjects;

class ScheduleParametersResource extends Resource
{
    public $starting_from;
    public $ending_at;
    public $working_from;
    public $working_to;
    public $number_of_rooms;
    public $max_defenses_per_professor;
    public $max_rooms;
    public $minutes_per_slot;

    protected static ?string $model = ScheduleParameters::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('starting_from')
                    ->required(),
                Forms\Components\DatePicker::make('ending_at')
                    ->required(),
                Forms\Components\TextInput::make('working_from')
                    ->required(),
                Forms\Components\TextInput::make('working_to')
                    ->required(),
                Forms\Components\TextInput::make('number_of_rooms')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_defenses_per_professor')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_rooms')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('minutes_per_slot')
                    ->required()
                    ->numeric(),

                \Filament\Forms\Components\Actions::make([
                    // \Filament\Forms\Components\Actions\Action::make('ScheduleHeadOfDepartment')
                    AssignInternshipsToProjects::make('Assign Internships To Projects'),

                    ScheduleHeadOfJury::make('Schedule Head of Department')
                        // ->requiresConfirmation(),
                        // ->action(function (ScheduleHeadOfDepartment $ScheduleHeadOfDepartment) {
                        //     dd('action called')
                        //     // $scheduleHeadOfDepartment = new ScheduleHeadOfDepartment('Schedule Head of Department');
                        // })
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('starting_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('working_from'),
                Tables\Columns\TextColumn::make('working_to'),
                Tables\Columns\TextColumn::make('number_of_rooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_defenses_per_professor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_rooms')
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
