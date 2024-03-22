<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InternshipAgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'internship_agreements';

    protected static ?string $inverseRelationship = 'project';

    protected static ?string $title = 'Internship agreements';

    protected static bool $isLazy = false;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(self::$title);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    // ->relationship('student', 'full_name')
                    ->required(),
                Forms\Components\TextInput::make('student.full_name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->searchable(false)
            ->recordTitleAttribute('title', 'student.full_name')
            ->columns([
                Tables\Columns\TextColumn::make('id_pfe'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('student.full_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['title', 'id_pfe'])
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder(__('Search by title or id_pfe...')),
                    ),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
