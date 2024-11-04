<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\MidweekEventSessionResource\Pages;
use App\Filament\Core\BaseResource;
use App\Filament\Imports\MidweekEventSessionImporter;
use App\Models\MidweekEventSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class MidweekEventSessionResource extends BaseResource
{
    protected static ?string $model = MidweekEventSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Midweek Event Session';

    protected static ?string $pluralModelLabel = 'Midweek Event Sessions';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationParentItem = 'Organization Accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('session_start_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('session_end_at')
                    ->required(),
                Forms\Components\Textarea::make('session_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_start_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_end_at')
                    ->dateTime()
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(MidweekEventSessionImporter::class),
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
            'index' => Pages\ListMidweekEventSessions::route('/'),
            'create' => Pages\CreateMidweekEventSession::route('/create'),
            'view' => Pages\ViewMidweekEventSession::route('/{record}'),
            'edit' => Pages\EditMidweekEventSession::route('/{record}/edit'),
        ];
    }
}
