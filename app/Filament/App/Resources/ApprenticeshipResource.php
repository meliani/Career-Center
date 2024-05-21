<?php

namespace App\Filament\App\Resources;

use App\Filament\Actions\Action\Processing\GenerateApprenticeshipAgreementAction;
use App\Filament\App\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\Apprenticeship;
use App\Services\Filament\Forms\ApprenticeshipAgreementForm;
use Filament;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
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
        return $form->schema((new ApprenticeshipAgreementForm())->getSchema());
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
                Tables\Actions\EditAction::make(),
                GenerateApprenticeshipAgreementAction::make('Generate Apprenticeship Agreement PDF')
                    ->label(__('Generate Apprenticeship Agreement PDF'))
                    ->requiresConfirmation(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])->headerActions([
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Internship agreement and validation process'))
                    ->headerActions([
                        // Infolists\Components\Actions\Action::make('edit page', 'edit')
                        //     ->label('Edit')
                        //     ->icon('heroicon-o-pencil')
                        //     ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                        //     ->tooltip('Edit this internship agreement')
                        //     ->url(fn ($record) => \App\Filament\App\Resources\ApprenticeshipResource::getUrl('edit', [$record->id])),
                    ])
                    ->schema([

                        Infolists\Components\Fieldset::make('Internship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('description')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('organization_name')
                                    ->label('Organization name'),
                                Infolists\Components\TextEntry::make('id_pfe')
                                    ->label('ID PFE'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('assigned_department')
                                    ->label('Assigned department'),
                            ]),
                        Infolists\Components\Fieldset::make('Administative dates')
                            ->schema([
                                Infolists\Components\TextEntry::make('announced_at')
                                    ->date()
                                    ->label('Announced at'),
                                Infolists\Components\TextEntry::make('validated_at')
                                    ->date()
                                    ->label('Validated at'),
                                Infolists\Components\TextEntry::make('received_at')
                                    ->date()
                                    ->label('Received at'),
                                Infolists\Components\TextEntry::make('signed_at')
                                    ->date()
                                    ->label('Signed at'),
                            ])
                            ->columns(4),

                    ]),
            ]);
    }
}
