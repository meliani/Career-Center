<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\MidweekEventResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\MidweekEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MidweekEventResource extends BaseResource
{
    protected static ?string $model = MidweekEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Midweek Event';

    protected static ?string $pluralModelLabel = 'Midweek Events';

    protected static ?string $navigationGroup = 'Midweek Pro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                // Forms\Components\TextInput::make('participation_status')
                //     ->maxLength(255),
                Forms\Components\Select::make('participation_status')
                    ->options(\App\Enums\EventParticipationStatus::class)
                    ->required(),
                Forms\Components\Select::make('organization_account_id')
                    ->relationship('organizationAccount', 'name')
                    ->required(),
                Forms\Components\Select::make('meeting_confirmed_by')
                    ->relationship('meetingConfirmedBy', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('meeting_confirmed_at'),
                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                Forms\Components\Select::make('midweek_event_session_id')
                    ->relationship('midweekEventSession', 'session_start_at')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('participation_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organizationAccount.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meetingConfirmedBy.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meeting_confirmed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('midweekEventSession.session_start_at')
                    ->sortable(),
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
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListMidweekEvents::route('/'),
            'create' => Pages\CreateMidweekEvent::route('/create'),
            'view' => Pages\ViewMidweekEvent::route('/{record}'),
            'edit' => Pages\EditMidweekEvent::route('/{record}/edit'),
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
