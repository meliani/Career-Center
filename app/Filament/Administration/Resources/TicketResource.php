<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\Section::make('Ticket')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),

                    ]),
                Forms\Components\Section::make('Ticket handling')
                    ->columns(2)
                    ->visible(fn () => auth()->user()->role == Enums\Role::SuperAdministrator)
                    ->schema([
                        Forms\Components\TextInput::make('user.name'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Open' => 'Open',
                                'Closed' => 'Closed',
                                'Pending' => 'Pending',
                                'Resolved' => 'Resolved',
                                'Unresolved' => 'Unresolved',
                                '' => 'Undefined',
                            ])
                            ->required(),
                        Forms\Components\Select::make('closed_reason')
                            ->options([
                                'Resolved' => 'Resolved',
                                'Duplicate' => 'Duplicate',
                                'Invalid' => 'Invalid',
                                'Unrelated' => 'Unrelated',
                                'unresolved' => 'Unresolved',
                                '' => 'Undefined',
                            ]),
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'name')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('closed_at')
                            ->nullable(),
                    ]),
                Forms\Components\Section::make('Response')
                    ->schema([

                        Forms\Components\Textarea::make('response')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('status'),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description(__('Created tickets will be considered as a knowledge base for the future, And will be readable by all collegues'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('response'),
                Tables\Columns\ColumnGroup::make('Ticket handling', [
                    Tables\Columns\TextColumn::make('user.name')
                        ->badge()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('assignedTo.name')
                        ->badge()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('closed_at')
                        ->dateTime()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('closed_reason')
                        ->badge()
                        ->sortable(),
                ]),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
