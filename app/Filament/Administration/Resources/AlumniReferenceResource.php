<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\AlumniReferenceResource\Pages;
use App\Filament\Core\BaseResource;
use App\Filament\Imports;
use App\Models\AlumniReference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class AlumniReferenceResource extends BaseResource
{
    protected static ?string $model = AlumniReference::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Alumni Reference';

    protected static ?string $pluralModelLabel = 'Alumni References';

    protected static ?string $navigationParentItem = 'Alumni Accounts';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('graduation_year_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('degree'),
                Forms\Components\TextInput::make('assigned_program'),
                Forms\Components\TextInput::make('is_enabled')
                    ->numeric(),
                Forms\Components\TextInput::make('is_mobility')
                    ->numeric(),
                Forms\Components\TextInput::make('abroad_school')
                    ->maxLength(191),
                Forms\Components\TextInput::make('work_status'),
                Forms\Components\TextInput::make('resume_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('avatar_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_of_bounces')
                    ->numeric(),
                Forms\Components\TextInput::make('bounce_reason')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graduation_year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('degree'),
                Tables\Columns\TextColumn::make('assigned_program'),
                Tables\Columns\TextColumn::make('is_enabled')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_mobility')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('abroad_school')
                    ->searchable(),
                Tables\Columns\TextColumn::make('work_status'),
                Tables\Columns\TextColumn::make('resume_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avatar_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_bounces')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bounce_reason')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(Imports\AlumniReferenceImporter::class),
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
            'index' => Pages\ListAlumniReferences::route('/'),
            'create' => Pages\CreateAlumniReference::route('/create'),
            'view' => Pages\ViewAlumniReference::route('/{record}'),
            'edit' => Pages\EditAlumniReference::route('/{record}/edit'),
        ];
    }
}
