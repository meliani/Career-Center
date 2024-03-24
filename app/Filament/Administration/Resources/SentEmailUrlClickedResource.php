<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\SentEmailUrlClickedResource\Pages;
use App\Filament\Core;
use App\Models\SentEmailUrlClicked;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class SentEmailUrlClickedResource extends Core\BaseResource
{
    protected static ?string $model = SentEmailUrlClicked::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Emails';

    protected static ?string $modelLabel = 'Sent Email URL Clicked';

    protected static ?string $pluralModelLabel = 'Sent Emails URLs Clicked';

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator();
        }

        return false;
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sent_email_id')
                    ->email()
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('hash')
                    ->required()
                    ->maxLength(32),
                Forms\Components\TextInput::make('clicks')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sent_email_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hash')
                    ->searchable(),
                Tables\Columns\TextColumn::make('clicks')
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
            'index' => Pages\ListSentEmailUrlClickeds::route('/'),
            'create' => Pages\CreateSentEmailUrlClicked::route('/create'),
            'edit' => Pages\EditSentEmailUrlClicked::route('/{record}/edit'),
        ];
    }
}
