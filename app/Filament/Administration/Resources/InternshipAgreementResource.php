<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Filament\Administration\Resources\InternshipAgreementResource\RelationManagers;
use App\Models\InternshipAgreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Enums;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternshipAgreementResource extends Resource
{
    protected static ?string $model = InternshipAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->numeric(),
                Forms\Components\TextInput::make('id_pfe')
                    ->numeric(),
                Forms\Components\TextInput::make('organization_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('adresse')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('country')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('office_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('parrain_titre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('parrain_nom')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('parrain_prenom')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('parrain_fonction')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('parrain_tel')
                    ->tel()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('parrain_mail')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_titre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_nom')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_prenom')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_fonction')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_tel')
                    ->tel()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('encadrant_ext_mail')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Textarea::make('title')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keywords')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('starting_at')
                    ->required(),
                Forms\Components\DatePicker::make('ending_at')
                    ->required(),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('load')
                    ->maxLength(191),
                Forms\Components\TextInput::make('int_adviser_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('year_id')
                    ->numeric(),
                Forms\Components\Toggle::make('is_valid'),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('Status'))
                    ->options(Enums\Status::class)
                    ->inline()
                    ->required(),
                    
                Forms\Components\DateTimePicker::make('announced_at'),
                Forms\Components\DateTimePicker::make('validated_at'),
                Forms\Components\Select::make('assigned_department')
                ->options(Enums\Department::class),
                Forms\Components\DateTimePicker::make('received_at'),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\TextInput::make('project_id')
                    ->numeric(),
                    Forms\Components\Select::make('binome_user_id')
                    ->options(function () {
                        return Student::all()->pluck('full_name', 'id');
                    }),
                Forms\Components\TextInput::make('partner_internship_id')
                    ->numeric(),
                Forms\Components\TextInput::make('partnership_status')
                    ->maxLength(50),
                Forms\Components\Textarea::make('observations')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_pfe')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adresse')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_fonction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_tel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parrain_mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_fonction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_tel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('encadrant_ext_mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starting_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('load')
                    ->searchable(),
                Tables\Columns\TextColumn::make('int_adviser_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_valid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('announced_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('binome_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner_internship_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partnership_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListInternshipAgreements::route('/'),
            'create' => Pages\CreateInternshipAgreement::route('/create'),
            'edit' => Pages\EditInternshipAgreement::route('/{record}/edit'),
        ];
    }
}
