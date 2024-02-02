<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\JuryResource\Pages;
use App\Filament\Administration\Resources\JuryResource\RelationManagers;
use App\Models\Jury;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JuryResource extends Resource
{
    protected static ?string $model = Jury::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('project_id')
                //     ->required()
                //     ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                    // add timeslot informations
                Tables\Columns\TextColumn::make('timeslot.start_time')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('timeslot.end_time')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('professors.name')
                    ->sortable()
                    ->searchable(),
                    // ->relationships('professors', 'name'),
                    // ->displayUsing(fn (Jury $jury) => $jury->professors->pluck('name')->join(', ')),
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
            RelationManagers\ProfessorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJuries::route('/'),
            // 'create' => Pages\CreateJury::route('/create'),
            'edit' => Pages\EditJury::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
       return false;
    }
}
