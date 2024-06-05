<?php

namespace App\Filament\Administration\Resources;

use App\Enums\EntrepriseContactCategory;
use App\Enums\Title;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\EntrepriseContactsResource\Pages;
use App\Models\EntrepriseContacts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EntrepriseContactsResource extends Resource
{
    protected static ?string $model = EntrepriseContacts::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Entreprises';

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator();
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('title')
                    ->options(Title::class)
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('company')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('alumni_promotion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('years_of_interactions_with_students')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_of_bounces')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_account_disabled')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('long_full_name')
                    ->searchable(false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('years_of_interactions_with_students')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alumni_promotion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_bounces')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_account_disabled')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('category')
                    ->options(EntrepriseContactCategory::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendSecondYearMailingCampaign::make('Send Second Year Mailing Campaign')
                        ->requiresConfirmation(),
                ]),
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
            'index' => Pages\ListEntrepriseContacts::route('/'),
            'create' => Pages\CreateEntrepriseContacts::route('/create'),
            'edit' => Pages\EditEntrepriseContacts::route('/{record}/edit'),
        ];
    }
}
