<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\FinalYearInternshipAgreementResource\Pages;
use App\Models\FinalYearInternship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinalYearInternshipAgreementResource extends Resource
{
    protected static ?string $model = FinalYearInternship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('year_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('project_id')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('announced_at'),
                Forms\Components\DateTimePicker::make('validated_at'),
                Forms\Components\TextInput::make('assigned_department'),
                Forms\Components\DateTimePicker::make('received_at'),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\Textarea::make('observations')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('organization_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('office_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('starting_at'),
                Forms\Components\DateTimePicker::make('ending_at'),
                Forms\Components\TextInput::make('remuneration')
                    ->numeric(),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(255),
                Forms\Components\TextInput::make('workload')
                    ->numeric(),
                Forms\Components\TextInput::make('parrain_id')
                    ->numeric(),
                Forms\Components\TextInput::make('external_supervisor_id')
                    ->numeric(),
                Forms\Components\TextInput::make('internal_supervisor_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pdf_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pdf_file_name')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('cancelled_at'),
                Forms\Components\Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_signed_by_student'),
                Forms\Components\Toggle::make('is_signed_by_organization'),
                Forms\Components\Toggle::make('is_signed_by_administration'),
                Forms\Components\DateTimePicker::make('signed_by_student_at'),
                Forms\Components\DateTimePicker::make('signed_by_organization_at'),
                Forms\Components\DateTimePicker::make('signed_by_administration_at'),
                Forms\Components\TextInput::make('verification_document_url')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('announced_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_department'),
                Tables\Columns\TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starting_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workload')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parrain_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('external_supervisor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internal_supervisor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pdf_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pdf_file_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cancelled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_signed_by_student')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_signed_by_organization')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_signed_by_administration')
                    ->boolean(),
                Tables\Columns\TextColumn::make('signed_by_student_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_by_organization_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_by_administration_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_document_url')
                    ->searchable(),
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
            'index' => Pages\ListFinalYearInternships::route('/'),
            'create' => Pages\CreateFinalYearInternship::route('/create'),
            'view' => Pages\ViewFinalYearInternship::route('/{record}'),
            'edit' => Pages\EditFinalYearInternship::route('/{record}/edit'),
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
