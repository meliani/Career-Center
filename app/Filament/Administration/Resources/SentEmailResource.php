<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\SentEmailResource\Pages;
use App\Filament\Core;
use App\Models\SentEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class SentEmailResource extends Core\BaseResource
{
    protected static ?string $model = SentEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Emails';

    protected static ?string $modelLabel = 'Opened Email';

    protected static ?string $pluralModelLabel = 'Opened Emails';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdministrator();
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hash')
                    ->required()
                    ->maxLength(32),
                Forms\Components\Textarea::make('headers')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sender_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sender_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('recipient_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('recipient_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('opens')
                    ->numeric(),
                Forms\Components\TextInput::make('clicks')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('opened_at'),
                Forms\Components\DateTimePicker::make('clicked_at'),
                Forms\Components\TextInput::make('message_id')
                    ->maxLength(255),
                Forms\Components\Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hash')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('opens')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('opened_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clicked_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message_id')
                    ->searchable(),
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
            'index' => Pages\ListSentEmails::route('/'),
            'create' => Pages\CreateSentEmail::route('/create'),
            'edit' => Pages\EditSentEmail::route('/{record}/edit'),
        ];
    }
}
