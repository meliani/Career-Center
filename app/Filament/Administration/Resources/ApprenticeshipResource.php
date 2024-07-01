<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Apprenticeship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApprenticeshipResource extends BaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $sort = 1;

    protected static ?string $navigationBadgeTooltip = 'Announced apprenticeships';

    protected static ?string $navigationGroup = 'Students and projects';

    protected static ?string $modelLabel = 'Apprenticeship';

    protected static ?string $pluralModelLabel = 'Apprenticeships';

    protected static ?string $title = 'Announced apprenticeships';

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator() || auth()->user()->isDirection();
        }

        return false;
    }

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
                Forms\Components\Textarea::make('keywords')
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
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('supervisor_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tutor_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pdf_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pdf_file_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name'),
                Tables\Columns\TextColumn::make('student.level')
                    ->label('Level'),
                Tables\Columns\TextColumn::make('student.program')
                    ->label('Program'),
                Tables\Columns\TextColumn::make('organization.name'),
                Tables\Columns\TextColumn::make('organization.city')
                    ->Label('City'),
                Tables\Columns\TextColumn::make('organization.country')
                    ->Label('Country'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('starting_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('workload')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parrain.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tutor_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('pdf_path')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pdf_file_name')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('year_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('announced_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('received_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_location')
                    ->toggleable(isToggledHiddenByDefault: true)
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
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator()) === false)
                    ->outlined(),
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
