<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Actions\SendBulkEmail;
use App\Filament\Administration\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year_id')
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(5),
                Forms\Components\TextInput::make('pin')
                    ->numeric(),
                Forms\Components\TextInput::make('full_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('email_perso')
                    ->email()
                    ->maxLength(191),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\TextInput::make('cv')
                    ->maxLength(191),
                Forms\Components\TextInput::make('lm')
                    ->maxLength(191),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(191),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\TextInput::make('level')
                    ->maxLength(10),
                Forms\Components\TextInput::make('program')
                    ->maxLength(10),
                Forms\Components\Toggle::make('is_mobility'),
                Forms\Components\TextInput::make('abroad_school')
                    ->maxLength(191),
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\Toggle::make('model_status_id'),
                Forms\Components\DatePicker::make('graduated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_perso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cv')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_mobility')
                    ->boolean(),
                Tables\Columns\TextColumn::make('abroad_school')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('graduated_at')
                    ->date()
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
                SelectFilter::make('is_active')
                    ->multiple()
                    ->options([
                        '0' => __('Inactive'),
                        '1' => __('Active'),
                    ])->placeholder(__('Filter by status')),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                SendBulkEmail::make('send_emails'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStudents::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Students');
    }
}
