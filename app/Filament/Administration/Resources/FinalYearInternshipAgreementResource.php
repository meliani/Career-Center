<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Enums\Status;
use App\Filament\Administration\Resources\FinalYearInternshipAgreementResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Year;
use Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class FinalYearInternshipAgreementResource extends BaseResource
{
    protected static ?string $model = FinalYearInternshipAgreement::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationBadgeTooltip = 'Announced final year internships';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?string $modelLabel = 'Final Year Internship Agreement';

    protected static ?string $pluralModelLabel = 'Final Year Internship Agreements';

    protected static ?string $title = 'Final Year Internship Agreements';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Status::Signed)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organization_id')
                    ->relationship(
                        name: 'organization',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->active(),
                    )
                    ->required(),
                Forms\Components\TextInput::make('office_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\SpatieTagsInput::make('tags')
                    ->type('internships')
                    ->splitKeys(['Tab', ',', ' '])
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('starting_at'),
                Forms\Components\DateTimePicker::make('ending_at'),
                Forms\Components\ToggleButtons::make('status')
                    ->options(Enums\Status::class)
                    ->required()
                    ->inline(),
                Forms\Components\Fieldset::make('Dates')
                    ->columns(2)
                    ->schema([
                        // Forms\Components\DateTimePicker::make('announced_at'),
                        Forms\Components\DateTimePicker::make('validated_at')
                            ->seconds(false)
                            ->native(false)
                            ->minutesStep(15)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Africa/Casablanca'),
                        Forms\Components\DateTimePicker::make('received_at')
                            ->seconds(false)
                            ->native(false)
                            ->minutesStep(15)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Africa/Casablanca'),
                        Forms\Components\DateTimePicker::make('signed_at')
                            ->seconds(false)
                            ->native(false)
                            ->minutesStep(15)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Africa/Casablanca'),
                    ]),
                Forms\Components\RichEditor::make('description')
                    ->label('Internship description')
                    ->columnSpan(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name']),

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
                Tables\Columns\TextColumn::make('student.level')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Level'),
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
                Tables\Columns\TextColumn::make('parrain.full_name')
                    ->sortable()
                    ->searchable(false),
                Tables\Columns\TextColumn::make('internalSupervisor.full_name')
                    ->searchable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('externalSupervisor.full_name')
                    ->searchable(false)
                    ->sortable(),
                Tables\Columns\SpatieTagsColumn::make('tags')
                    ->searchable(false),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()->hidden(fn () => auth()->user()->isAdministrator() === false),
                    // ->disabled(! auth()->user()->can('delete', $this->post)),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),

                ])
                    ->hidden((auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false)
                    ->hidden(true)
                    ->label('')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('View, edit, or delete this internship agreement')),
                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\Action\SignAction::make()
                        ->disabled(fn ($record): bool => $record['signed_at'] !== null),
                    \App\Filament\Actions\Action\ReceiveAction::make()
                        ->disabled(fn ($record): bool => $record['received_at'] !== null),
                    Tables\Actions\ActionGroup::make([
                        \App\Filament\Actions\Action\ValidateAction::make()
                            ->disabled(fn ($record): bool => $record['validated_at'] !== null),
                        \App\Filament\Actions\Action\AssignDepartmentAction::make()
                            ->disabled(fn ($record): bool => $record['assigned_department'] !== null),
                    ])
                        ->dropdown(false),
                ])
                    ->dropdownWidth(Filament\Support\Enums\MaxWidth::ExtraSmall)
                    ->label('')
                    ->icon('heroicon-o-bars-3')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('Validate, sign, or assign department'))
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false),

            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('sign')
                        ->label('Sign selection')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden(fn () => ! auth()->user()->can('sign', new FinalYearInternshipAgreement))
                        ->action(fn ($records) => $records->each->sign()),

                    Tables\Actions\BulkAction::make('validate')
                        ->label('Validate selection')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden(fn () => ! auth()->user()->can('validate', new FinalYearInternshipAgreement))
                        ->action(fn ($records) => $records->each->validate()),

                    Tables\Actions\BulkAction::make('achieve')
                        ->label('Achieve selection')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->hidden(fn () => ! auth()->user()->can('achieve', new FinalYearInternshipAgreement))
                        ->action(fn ($records) => $records->each->achieve()),
                ])
                    ->hidden(fn () => ! auth()->user()->can('manage', new FinalYearInternshipAgreement)),
            ])
            ->headerActions([
                \App\Filament\Actions\Action\AssignFinalInternshipsToProjects::make('Assign to Projects')
                    ->hidden(fn () => ! auth()->user()->can('assignToProject', FinalYearInternshipAgreement::class))
                    ->outlined(),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator()) === false)
                    ->outlined(),
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
            'index' => Pages\ListFinalYearInternshipAgreements::route('/'),
            'create' => Pages\CreateFinalYearInternshipAgreement::route('/create'),
            'view' => Pages\ViewFinalYearInternshipAgreement::route('/{record}'),
            'edit' => Pages\EditFinalYearInternshipAgreement::route('/{record}/edit'),
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
                Infolists\Components\Section::make('Internship Agreement')
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
                                Infolists\Components\TextEntry::make('agreement_pdf_url')
                                    ->label('Agreement PDF')
                                    // ->simpleLightbox($infolist->getRecord()->agreement_pdf_url)
                                    ->visible(fn ($record) => $record->agreement_pdf_url)
                                    ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? __('Download agreement PDF') : __('Not generated yet!'))

                                    ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true),
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
                                Infolists\Components\TextEntry::make('externalSupervisor.full_name')
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
                                Infolists\Components\SpatieTagsEntry::make('tags')
                                    ->label('Tags')
                                    ->type('internships')
                                    ->visible(fn ($record) => $record->tags->isNotEmpty()),
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
