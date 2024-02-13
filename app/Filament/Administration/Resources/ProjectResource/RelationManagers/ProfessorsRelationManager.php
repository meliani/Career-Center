<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums;
class ProfessorsRelationManager extends RelationManager
{
    protected static string $relationship = 'professors';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\Select::make('role')
                    ->options(Enums\JuryRole::class)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('jury_role'),
                Tables\Columns\TextColumn::make('role'),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                ->form(fn (Tables\Actions\AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('jury_role')->options(Enums\JuryRole::class),
                    // Forms\Components\Select::make('is_president')->options([
                    //     '0' => 'No',
                    //     '1' => 'Yes',
                    // ]),
                ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
