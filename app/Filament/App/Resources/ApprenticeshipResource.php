<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Filament\Actions;
use App\Filament\App\Resources\ApprenticeshipResource\Pages;
use App\Models\Apprenticeship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApprenticeshipResource extends Resource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $softDelete = true;

    protected static ?string $modelLabel = 'Apprenticeship';

    protected static ?string $pluralModelLabel = 'Apprenticeships';

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
            ->schema([
                Forms\Components\Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('city')
                            ->required(),
                        Forms\Components\TextInput::make('country')
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('title')
                    ->columnSpanFull()->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\SpatieTagsInput::make('keywords'),
                Forms\Components\Fieldset::make(__('Internship dates'))
                    ->columns(4)
                    ->schema([
                        /* parrain_id
                        supervisor_id
                        tutor_id */
                        Forms\Components\Select::make('parrain_id')
                            ->relationship('parrain', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\Select::make('organization_id')
                                    ->relationship('organization', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\TextInput::make('city')
                                            ->required(),
                                        Forms\Components\TextInput::make('country')
                                            ->required(),
                                    ]),
                                Forms\Components\Select::make('title')
                                    ->options(Enums\Title::class)
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->required(),
                                Forms\Components\TextInput::make('function')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required(),
                                Forms\Components\Select::make('role')
                                    ->options(Enums\OrganizationContactRole::class)
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('supervisor_id')
                            ->relationship('supervisor', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\Select::make('title')
                                    ->options(Enums\Title::class)
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->required(),
                                Forms\Components\TextInput::make('function')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required(),
                                Forms\Components\Select::make('role')
                                    ->options(Enums\OrganizationContactRole::class)
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Fieldset::make(__('Internship dates'))
                    ->columns(4)
                    ->schema([
                        Forms\Components\DateTimePicker::make('starting_at'),
                        Forms\Components\DateTimePicker::make('ending_at'),
                    ]),
                Forms\Components\Fieldset::make(__('Remuneration and workload'))
                    ->columns(8)
                    ->schema([
                        Forms\Components\Select::make('currency')
                            ->default(Enums\Currency::MDH->getSymbol())
                            ->options([
                                Enums\Currency::EUR->getSymbol() => Enums\Currency::EUR->getSymbol(),
                                Enums\Currency::USD->getSymbol() => Enums\Currency::USD->getSymbol(),
                                Enums\Currency::MDH->getSymbol() => Enums\Currency::MDH->getSymbol(),
                            ])
                            ->live()
                            ->id('currency'),
                        Forms\Components\TextInput::make('remuneration')
                            ->numeric()
                            ->columnSpan(2)
                            // get prefix from crrency value
                            ->id('remuneration')
                            ->prefix(fn (Get $get) => $get('currency')),

                        Forms\Components\TextInput::make('workload')
                            ->placeholder('Hours / Week')
                            ->numeric(),
                    ]),
                Forms\Components\Fieldset::make(__('Internship documents'))
                    // ->columns(6)
                    ->schema([
                        \Filament\Forms\Components\Actions::make([
                            Actions\Action\Processing\GenerateApprenticeshipAgreementPdfAction::make('Generate Apprenticeship Agreement PDF')
                                ->label(__('Generate Apprenticeship Agreement PDF'))
                                ->requiresConfirmation(),
                        ]),
                    ]),
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
                Tables\Actions\EditAction::make(),
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
