<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\InternshipApplicationResource\Pages;
use App\Models\InternshipApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InternshipApplicationResource extends Resource
{
    protected static ?string $model = InternshipApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('internship_offer_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['student', 'internshipOffer'])->where('student_id', auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->heading(__('My Internship Applications'))
            ->contentGrid(
                [
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                    '2xl' => 1,
                ]
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internshipOffer.organization_name')
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
            'index' => Pages\ListInternshipApplications::route('/'),
            // 'create' => Pages\CreateInternshipApplication::route('/create'),
            // 'edit' => Pages\EditInternshipApplication::route('/{record}/edit'),
        ];
    }
}
