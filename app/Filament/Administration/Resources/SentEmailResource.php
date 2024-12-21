<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\SentEmailResource\Pages;
use App\Filament\Core;
use App\Models\SentEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\View\View;

class SentEmailResource extends Core\BaseResource
{
    protected static ?string $model = SentEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Mailing';

    protected static ?string $modelLabel = 'Sent Email';

    protected static ?string $pluralModelLabel = 'Sent Emails';

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator();
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
                Forms\Components\RichEditor::make('content')
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('hash')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('opens')
                    ->numeric()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('clicks')
                    ->numeric()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                Tables\Columns\TextColumn::make('opened_at')
                    ->dateTime()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('clicked_at')
                    ->dateTime()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('message_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Full Details'),
                Tables\Actions\Action::make('preview')
                    ->label('Quick Preview')
                    ->icon('heroicon-o-envelope')
                    ->modalHeading(fn (SentEmail $record): string => "Email Preview: {$record->subject}")
                    ->modalContent(
                        fn (SentEmail $record): View => view(
                            'filament.resources.sent-email-resource.preview-modal',
                            ['record' => $record]
                        )
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl'),
                // Tables\Actions\EditAction::make(),
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
            // 'create' => Pages\CreateSentEmail::route('/create'),
            'view' => Pages\ViewSentEmail::route('/{record}'),
            // 'edit' => Pages\EditSentEmail::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Email Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('subject')
                                        ->label('Subject')
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    Infolists\Components\TextEntry::make('created_at')
                                        ->label('Sent At')
                                        ->dateTime(),
                                ]),
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('sender_name')
                                        ->label('From')
                                        ->default(fn ($record) => $record->sender_email),
                                    Infolists\Components\TextEntry::make('recipient_name')
                                        ->label('To')
                                        ->default(fn ($record) => $record->recipient_email),
                                ]),
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('opens')
                                            ->label('Opens')
                                            ->icon('heroicon-o-eye')
                                            ->color('success')
                                            ->size('lg')
                                            ->alignment('center'),
                                        Infolists\Components\TextEntry::make('clicks')
                                            ->label('Clicks')
                                            ->icon('heroicon-o-cursor-arrow-rays')
                                            ->color('info')
                                            ->size('lg')
                                            ->alignment('center'),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Email Content')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label(false)
                            ->html()
                            // ->prose()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Technical Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('message_id')
                                    ->label('Message ID')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('hash')
                                    ->label('Hash')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('opened_at')
                                    ->label('First Opened')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('clicked_at')
                                    ->label('First Clicked')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
