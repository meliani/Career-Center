<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\DeliberationPVResource\Pages;
use App\Models\DeliberationPV;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeliberationPVResource extends Resource
{
    protected static ?string $model = DeliberationPV::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('meeting_date')
                    ->required(),
                // Forms\Components\TextInput::make('attendees')
                //     ->required(),

                Forms\Components\MarkdownEditor::make('decisions')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\MarkdownEditor::make('remarks')
                    ->columnSpanFull(),
                Forms\Components\Select::make('attendees')
                    ->options(\App\Models\Professor::all()->pluck('name', 'id'))
                    ->multiple()
                    ->required()
                    ->rules('required', 'array'),
                Forms\Components\Select::make('year_id')
                    ->options(\App\Models\Year::all()->pluck('title', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('qr_code'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meeting_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_id')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_verification_qr_code')
                    ->label('Generate QR Code')
                    // ->icon('heroicon-o-qrcode')
                    ->action(fn (DeliberationPV $record) => $record->generateVerificationQRCode()),
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
            'index' => Pages\ListDeliberationPVS::route('/'),
            'create' => Pages\CreateDeliberationPV::route('/create'),
            'view' => Pages\ViewDeliberationPV::route('/{record}'),
            'edit' => Pages\EditDeliberationPV::route('/{record}/edit'),
        ];
    }
}
