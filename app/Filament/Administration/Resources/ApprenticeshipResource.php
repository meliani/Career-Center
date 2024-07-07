<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Apprenticeship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ApprenticeshipResource extends BaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

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
                // Forms\Components\TextInput::make('student.name')
                //     ->disabled(),
                // Forms\Components\TextInput::make('year_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('project_id')
                //     ->numeric(),
                Forms\Components\ToggleButtons::make('status')
                    ->options(Enums\Status::class)
                    ->required()
                    ->inline(),
                // Forms\Components\DateTimePicker::make('announced_at'),
                // Forms\Components\DateTimePicker::make('validated_at'),
                // Forms\Components\TextInput::make('assigned_department'),
                // Forms\Components\DateTimePicker::make('received_at'),
                // Forms\Components\DateTimePicker::make('signed_at'),
                // Forms\Components\Textarea::make('observations')
                //     ->columnSpanFull(),
                Forms\Components\Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required(),
                // Forms\Components\TextInput::make('office_location')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                // Forms\Components\Textarea::make('description')
                //     ->columnSpanFull(),
                // Forms\Components\Textarea::make('keywords')
                //     ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('starting_at'),
                Forms\Components\DateTimePicker::make('ending_at'),
                // Forms\Components\TextInput::make('remuneration')
                //     ->numeric(),
                // Forms\Components\TextInput::make('currency')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('workload')
                //     ->numeric(),
                // Forms\Components\TextInput::make('parrain_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('supervisor_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('tutor_id')
                //     ->numeric(),
                // Forms\Components\TextInput::make('pdf_path')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('pdf_file_name')
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name']),
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->date()
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
                    ->sortable()
                    ->searchable(false),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->searchable(false)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('tutor_id')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('pdf_path')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pdf_file_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('year_id')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('project_id')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cancellation_reason')
                    ->toggleable(isToggledHiddenByDefault: true)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        $verification_document_url = Storage::disk('cancellation_verification')->url($infolist->getRecord()->verification_document_url);

        return $infolist
            ->schema([
                Infolists\Components\Section::make('Apprenticeship Agreement')
                    ->columns(3) // Adjust the number of columns as needed
                    ->schema([
                        Infolists\Components\Fieldset::make('Basic Information')
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->columnSpan(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('organization.name')
                                    ->label('Organization'),
                            ]),
                        Infolists\Components\Fieldset::make('Organization details')
                            ->columnSpan(1)
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('organization.city')
                                    ->label('City'),
                                Infolists\Components\TextEntry::make('organization.country')
                                    ->label('Country'),
                                Infolists\Components\TextEntry::make('office_location')
                                    ->label('Office Location')
                                    ->visible(fn ($record) => $record->office_location),

                            ]),
                        Infolists\Components\Fieldset::make('Dates')
                            ->columnSpan(1)
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('starting_at')
                                    ->label('Starting at')
                                    ->date(),
                                Infolists\Components\TextEntry::make('ending_at')
                                    ->label('Ending at')
                                    ->date(),
                            ]),
                        Infolists\Components\Fieldset::make('Remuneration')
                            ->columnSpan(1)
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                // Infolists\Components\TextEntry::make('remuneration')
                                //     ->label('Amount'),
                                // Infolists\Components\TextEntry::make('currency')
                                //     ->label('Currency'),
                                Infolists\Components\TextEntry::make('remuneration')
                                    ->money(fn ($record) => $record->currency->getLabel())
                                    ->placeholder(__('No remuneration specified')),

                                Infolists\Components\TextEntry::make('workload')
                                    ->placeholder(__('No workload specified')),
                            ]),
                        Infolists\Components\Fieldset::make('Supervisors')
                            ->columnSpan(1)
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('parrain.full_name')
                                    ->label('Parrain'),
                                Infolists\Components\TextEntry::make('supervisor.full_name')
                                    ->label('Supervisor'),
                            ]),
                        Infolists\Components\Fieldset::make('Status')
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('assigned_department')
                                    ->label('Assigned Department')
                                    ->visible(fn ($record) => $record->assigned_department),
                                Infolists\Components\TextEntry::make('cancellation_reason')
                                    ->label('Cancellation Reason')
                                    ->visible(fn ($record) => $record->appliedCancellation()),
                                Infolists\Components\TextEntry::make('verification_document_url')
                                    ->label('Verification Document')
                                    // ->disk('cancellation_verification')
                                    // ->visibility('private')
                                    ->visible(fn ($record) => $record->appliedCancellation())
                                    ->simpleLightbox($verification_document_url),
                            ]),
                        Infolists\Components\Fieldset::make('Dates')
                            ->columnSpan(2)
                            ->visible(fn ($record) => ($record->announced_at || $record->validated_at || $record->received_at || $record->signed_at))
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('announced_at')
                                    ->label('Announced at')
                                    ->date()
                                    ->visible(fn ($record) => $record->announced_at),
                                Infolists\Components\TextEntry::make('validated_at')
                                    ->label('Validated at')
                                    ->date()
                                    ->visible(fn ($record) => $record->validated_at),

                                Infolists\Components\TextEntry::make('received_at')
                                    ->label('Received at')
                                    ->date()
                                    ->visible(fn ($record) => $record->received_at),
                                Infolists\Components\TextEntry::make('signed_at')
                                    ->label('Signed at')
                                    ->date()
                                    ->visible(fn ($record) => $record->signed_at),

                            ]),
                        Infolists\Components\Fieldset::make('System Dates')
                            ->columnSpan(1)
                            ->columns(3) // Adjust for each Fieldset as needed
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created at'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Updated at'),
                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Deleted at')
                                    ->visible(fn ($record) => $record->trashed()),
                            ]),
                    ]),
            ]);

    }
}
