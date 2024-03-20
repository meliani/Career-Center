<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static bool $isLazy = false;

    // protected static ?string $inverseRelationship = 'projects';
    protected static ?string $title = 'Students';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(self::$title);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    // ->relationship('student', 'full_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('program'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
            ])

            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                // dettach student from existing project et detach his agreement from any project
                // ->before(function (Model $ownerRecord) {
                //     $ownerRecord->projects()->detach();
                //     $ownerRecord->internship()->detach();
                // })
                // // we'll attach current project to student and his internship to current project
                // ->after(function (Model $ownerRecord) {
                //     $ownerRecord->projects()->attach(request('record'));
                //     $ownerRecord->internship()->attach(request('record'));
                // }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
