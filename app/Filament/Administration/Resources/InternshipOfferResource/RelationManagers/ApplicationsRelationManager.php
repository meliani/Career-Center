<?php

namespace App\Filament\Administration\Resources\InternshipOfferResource\RelationManagers;

use App\Filament\Administration\Resources\StudentResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public static $primaryColumn = 'student.name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         // Forms\Components\TextInput::make('student.name')
        //         //     ->required()
        //         //     ->maxLength(255),
        //     ]);
        return StudentResource::form($form);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('student.level')
                    ->label('Level'),
                Tables\Columns\TextColumn::make('student.email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('student.phone')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('student.email_perso')
                    ->label('Email perso'),
                Tables\Columns\TextColumn::make('student.cv')
                    ->label('CV'),
                Tables\Columns\TextColumn::make('student.lm')
                    ->label('Cover letter'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Filament\Actions\Action\SendApplicationsEmailAction::make(),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label(false),
            ], position: \Filament\Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->recruiting_type === \App\Enums\RecruitingType::SchoolManaged;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
