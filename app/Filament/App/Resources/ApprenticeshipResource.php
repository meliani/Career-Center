<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\Apprenticeship;
use App\Services\Filament\Forms\ApprenticeshipAgreementForm;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\URL;

class ApprenticeshipResource extends StudentBaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $softDelete = true;

    protected static ?string $modelLabel = 'Apprenticeship agreement';

    protected static ?string $pluralModelLabel = 'Apprenticeship agreements';

    public static function getModelLabel(): string
    {
        return __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(ApprenticeshipAgreementForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('student_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('year_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('project_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pdf_file_name')
                    ->label('Agreement PDF')
                    ->limit(20)
                    ->url(fn (Apprenticeship $record) => URL::to($record->pdf_path . '/' . $record->pdf_file_name), shouldOpenInNewTab: true),
                // Tables\Columns\TextColumn::make('announced_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('validated_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('assigned_department'),
                // Tables\Columns\TextColumn::make('received_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('signed_at')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starting_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->dateTime()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('remuneration')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('currency')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('workload')
                //     ->numeric()
                //     ->sortable(),
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
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListApprenticeships::route('/'),
            'create' => Pages\CreateApprenticeship::route('/create'),
            'view' => Pages\ViewApprenticeship::route('/{record}'),
            'edit' => Pages\EditApprenticeship::route('/{record}/edit'),
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
