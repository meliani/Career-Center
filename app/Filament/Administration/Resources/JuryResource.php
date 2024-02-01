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
                Forms\Components\TextInput::make('jury_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('professor_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_president')
                    ->required(),
                Forms\Components\TextInput::make('role'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jury_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('professor_id')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('professor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_president')
                    ->boolean(),
                Tables\Columns\TextColumn::make('role')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJuries::route('/'),
        ];
    }
}
