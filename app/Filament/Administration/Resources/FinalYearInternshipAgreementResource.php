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
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel;

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
        return auth()->user()->can('viewAny', static::getModel());
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->isAdministrator()) {
            return static::getModel()::where('status', Status::Signed)->count();
        }

        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make(__('Important Dates'))
                            ->description(__('Key dates for the internship agreement process'))
                            ->icon('heroicon-o-calendar')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\DateTimePicker::make('validated_at')
                                    ->label('Validation Date')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('received_at')
                                    ->label('Reception Date')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('signed_at')
                                    ->label('Signature Date')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                            ]),

                        Forms\Components\Tabs::make('Agreement')
                            ->columnSpan(2)
                            ->tabs([

                                Forms\Components\Tabs\Tab::make(__('Schedule & Status'))
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Forms\Components\Section::make(__('Timeline'))
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('starting_at')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y'),
                                                Forms\Components\DateTimePicker::make('ending_at')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y'),
                                            ])->columns(2),

                                        Forms\Components\Section::make(__('Status'))
                                            ->schema([
                                                Forms\Components\ToggleButtons::make('status')
                                                    ->options(Enums\Status::class)
                                                    ->required()
                                                    ->inline()
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('Basic Information'))
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Section::make(__('Organization Details'))
                                            ->schema([
                                                Forms\Components\Select::make('organization_id')
                                                    ->relationship(
                                                        name: 'organization',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn ($query) => $query->active(),
                                                    )
                                                    ->required()
                                                    ->disabled(),
                                                Forms\Components\TextInput::make('office_location')
                                                    ->maxLength(255),
                                            ])->columns(2),

                                        Forms\Components\Section::make(__('Internship Details'))
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                Forms\Components\MarkdownEditor::make('description')
                                                    ->label('Internship description')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('Supervisors'))
                                    ->icon('heroicon-o-users')
                                    ->schema([
                                        Forms\Components\Section::make(__('External Supervisor'))
                                            ->description(__('The student\'s supervisor from the organization'))
                                            ->schema([
                                                Forms\Components\Select::make('external_supervisor_id')
                                                    ->label(__('External Supervisor'))
                                                    ->preload()
                                                    ->relationship(
                                                        name: 'externalSupervisor',
                                                        titleAttribute: 'full_name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                                                    )
                                                    ->getOptionLabelFromRecordUsing(
                                                        fn (Model $record) => "{$record->full_name} - {$record->function}"
                                                    )
                                                    ->searchable(['first_name', 'last_name'])
                                                    ->required()
                                                    ->createOptionForm([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('title')
                                                                    ->options(Enums\Title::class),
                                                                Forms\Components\TextInput::make('first_name')
                                                                    ->required()
                                                                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? ucwords($state) : null),
                                                                Forms\Components\TextInput::make('last_name')
                                                                    ->required()
                                                                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? ucwords($state) : null),
                                                                Forms\Components\TextInput::make('email')
                                                                    ->email()
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('phone')->tel()->required(),
                                                                Forms\Components\TextInput::make('function')->required(),
                                                            ]),
                                                    ])
                                                    ->createOptionUsing(function ($data, Get $get) {
                                                        $contact = new \App\Models\InternshipAgreementContact;
                                                        $contact->fill($data);
                                                        $contact->role = Enums\OrganizationContactRole::Mentor;
                                                        $contact->organization_id = $get('organization_id');
                                                        $contact->save();

                                                        return $contact->getKey();
                                                    })
                                                    ->editOptionForm([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('title')
                                                                    ->options(Enums\Title::class),
                                                                Forms\Components\TextInput::make('first_name')
                                                                    ->required()
                                                                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? ucwords($state) : null),
                                                                Forms\Components\TextInput::make('last_name')
                                                                    ->required()
                                                                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? ucwords($state) : null),
                                                                Forms\Components\TextInput::make('email')
                                                                    ->email()
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('phone')->tel()->required(),
                                                                Forms\Components\TextInput::make('function')->required(),
                                                            ]),
                                                    ]),
                                            ]),
                                    ]),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.id_pfe')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ID PFE')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->description(fn ($record) => $record->student->id_pfe),

                Tables\Columns\TextColumn::make('student.program')
                    ->label('Program')
                    ->tooltip(fn ($record) => $record->student->program->getDescription() . ' (' . __('Coordinator') . ': ' . $record->student->getProgramCoordinator()->full_name . ')'),

                Tables\Columns\TextColumn::make('organization.name')
                    ->description(fn ($record) => $record->organization->city . ', ' . $record->organization->country)
                    ->searchable()
                    ->label('Organization')
                    ->tooltip(fn ($record) => __('Organization Representative') . ': ' . $record->parrain->full_name),
                Tables\Columns\TextColumn::make('organization.city')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->Label('City'),
                Tables\Columns\TextColumn::make('organization.country')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->Label('Country'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => __('Start date') . ': ' . $record->starting_at->format('d/m/Y') . ' - ' . __('End date') . ': ' . $record->ending_at->format('d/m/Y')),
                Tables\Columns\TextColumn::make('starting_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ending_at')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(false),
                Tables\Columns\TextColumn::make('suggestedInternalSupervisor.full_name')
                    ->visible(fn () => auth()->user()->isAdministrator())
                    ->label('Internal Supervisor (student suggested)')
                    ->searchable(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('externalSupervisor.full_name')
                    ->searchable(false)
                    ->sortable()
                    ->description(fn ($record) => $record->externalSupervisor?->email)
                    ->tooltip(fn ($record) => $record->externalSupervisor?->phone),
                Tables\Columns\SpatieTagsColumn::make('tags')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->type('internships')
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('description')
                ->label(__('Description'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(false)
                ->width('xl')
                ->sortable(false)
                ->wrap()
                ->lineClamp(2)
                ->tooltip(
                    fn (FinalYearInternshipAgreement $internship) => $internship->description,
                ),
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
                // Tables\Columns\TextColumn::make('cancellation_reason')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),

                Tables\Columns\TextColumn::make('announced_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),

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
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->visible(fn () => auth()->user()->can('update', new FinalYearInternshipAgreement))
                    ->tooltip(__('Edit this internship agreement')),

                Tables\Actions\ActionGroup::make([
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

                    ])
                        ->dropdown(false),
                ])
                    ->dropdownWidth(Filament\Support\Enums\MaxWidth::ExtraSmall)
                    ->label('')
                    ->icon('heroicon-o-bars-3')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('Validate, sign, or assign department'))
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false)
                    ->visible(false),
                \App\Filament\Actions\Action\AssignDepartmentAction::make('assign_department')
                    ->label('Assign department')
                    ->disabled(fn ($record): bool => $record['assigned_department'] !== null)
                    ->visible(fn ($record) => $record['assigned_department'] === null)
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isProgramCoordinator()) === false),
                \App\Filament\Actions\Action\AssignDepartmentAction::make('edit_department')
                    ->label('edit department')
                    ->tooltip(__('Edit assigned department : old and new department coordinators will be notified'))
                    // visible when department exists and when the user is an administrator
                    ->visible(fn ($record) => $record['assigned_department'] !== null && auth()->user()->isAdministrator()),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkAction::make('sign')
                    ->label('Sign selection')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalIconColor('success')
                    ->modalIcon('heroicon-o-check')
                    ->modalHeading(__('Sign internship agreements'))
                    ->modalDescription(__('Are you sure you want to mark this internships as Signed?'))
                    ->modalSubmitActionLabel(__('Mark as signed'))
                    ->hidden(fn () => ! auth()->user()->can('sign', new FinalYearInternshipAgreement))
                    ->action(fn ($records) => $records->each->sign()),
                Tables\Actions\BulkActionGroup::make([

                    // Tables\Actions\BulkAction::make('validate')
                    //     ->label('Validate selection')
                    //     ->icon('heroicon-o-check-circle')
                    //     ->color('success')
                    //     ->requiresConfirmation()
                    //     ->hidden(fn () => ! auth()->user()->can('validate', new FinalYearInternshipAgreement))
                    //     ->action(fn ($records) => $records->each->validate()),

                    // Tables\Actions\BulkAction::make('achieve')
                    //     ->label('Achieve selection')
                    //     ->icon('heroicon-o-archive-box')
                    //     ->color('warning')
                    //     ->requiresConfirmation()
                    //     ->hidden(fn () => ! auth()->user()->can('achieve', new FinalYearInternshipAgreement))
                    //     ->action(fn ($records) => $records->each->receive()),
                ])
                    ->hidden(fn () => ! auth()->user()->can('manage', new FinalYearInternshipAgreement)),
            ])
            ->headerActions([
                \App\Filament\Actions\Action\AssignFinalInternshipsToProjects::make('Assign to Projects')
                    ->hidden(fn () => ! auth()->user()->can('assignToProject', FinalYearInternshipAgreement::class))
                    ->outlined(),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator()) === false)
                    ->exports([
                        FilamentExcel\Exports\ExcelExport::make()
                            ->askForFilename()
                            ->askForWriterType()
                            ->withFilename(fn ($filename) => 'carrieres-' . $filename)
                            ->fromTable()
                            ->ignoreFormatting([
                                'tags',
                            ])
                            ->withColumns([
                                FilamentExcel\Columns\Column::make('tags.name')->width(10)->heading(__('Tags')),
                            ]),
                    ])
                    ->tooltip(__('Displayed columns will be exported, you can change the columns to be exported from the table settings')),
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
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (auth()->user()->isProgramCoordinator()) {
            $query->whereHas('student', function (Builder $query) {
                $query->where('program', auth()->user()->assigned_program);
            });
        }

        if (auth()->user()->isDepartmentHead()) {
            $query->where('assigned_department', auth()->user()->department);
        }

        return $query;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(12)
            ->schema([
                Infolists\Components\Tabs::make('Relations')
                    ->columns(4)
                    ->columnSpan(8)
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Agreement Details'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\Section::make(__('Basic Information'))
                                    ->icon('heroicon-o-information-circle')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('student.long_full_name')
                                            ->label(__('Student'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->tooltip(fn ($record) => $record->student->long_full_name),

                                        Infolists\Components\TextEntry::make('student.program')
                                            ->label(__('Program'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->badge()
                                            ->tooltip(fn ($record) => $record->student->program->getDescription() . ' (' . __('Coordinator') . ': ' . $record->student->getProgramCoordinator()->full_name . ')'),

                                        Infolists\Components\TextEntry::make('organization.name')
                                            ->label(__('Organization'))
                                            ->icon('heroicon-o-building-office')
                                            ->badge()
                                            ->color('success')
                                            ->tooltip(fn ($record) => $record->organization->name),
                                    ]),

                                Infolists\Components\Section::make(__('Internship Details'))
                                    ->icon('heroicon-o-briefcase')
                                    ->columns(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('title')
                                            ->label(__('Title'))
                                            ->columnSpanFull()
                                            ->markdown(),

                                        Infolists\Components\TextEntry::make('description')
                                            ->label(__('Description'))
                                            ->columnSpanFull()
                                            ->markdown(),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Location & Schedule'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Infolists\Components\Section::make(__('Location'))
                                    ->icon('heroicon-o-building-office-2')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('organization.city')
                                            ->label(__('City'))
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('organization.country')
                                            ->label(__('Country'))
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('office_location')
                                            ->label(__('Office Location'))
                                            ->badge()
                                            ->visible(fn ($record) => $record->office_location),
                                    ]),

                                Infolists\Components\Section::make(__('Schedule & Compensation'))
                                    ->icon('heroicon-o-calendar')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('starting_at')
                                            ->label(__('Start Date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                        Infolists\Components\TextEntry::make('ending_at')
                                            ->label(__('End Date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                        Infolists\Components\TextEntry::make('workload')
                                            ->label(__('Workload'))
                                            ->icon('heroicon-o-clock')
                                            ->placeholder(__('No workload specified'))
                                            ->visible(fn ($record) => $record->workload),
                                        Infolists\Components\TextEntry::make('remuneration')
                                            ->label(__('Remuneration'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => ($record->currency->value))
                                            ->placeholder(__('No remuneration specified'))
                                            ->visible(fn ($record) => $record->remuneration),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Supervision'))
                            ->icon('heroicon-o-users')
                            ->schema([
                                Infolists\Components\Section::make(__('Supervisors'))
                                    ->icon('heroicon-o-user-group')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('externalSupervisor.full_name')
                                            ->label(__('External Supervisor'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('info')
                                            ->tooltip(fn ($record) => $record->externalSupervisor->full_name),

                                        Infolists\Components\TextEntry::make('parrain.full_name')
                                            ->label(__('Organization Representative'))
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('success')
                                            ->tooltip(fn ($record) => $record->parrain->full_name),

                                        Infolists\Components\TextEntry::make('assigned_department')
                                            ->label(__('Assigned Department'))
                                            ->icon('heroicon-o-building-library')
                                            ->badge()
                                            ->visible(fn ($record) => $record->assigned_department)
                                            ->tooltip(fn ($record) => $record->assigned_department->getDescription()),
                                    ]),
                            ]),
                    ]),

                Infolists\Components\Grid::make(4)
                    ->columnSpan(4)
                    ->schema([
                        Infolists\Components\Section::make(__('Status & Documents'))
                            ->icon('heroicon-o-document-check')
                            ->collapsible()
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->tooltip(fn ($record) => $record->status->value),

                                Infolists\Components\TextEntry::make('agreement_pdf_url')
                                    ->label(__('Agreement PDF'))
                                    ->icon('heroicon-o-document')
                                    ->visible(fn ($record) => $record->agreement_pdf_url)
                                    ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true)
                                    ->badge()
                                    ->color('success'),
                                    
                                Infolists\Components\TextEntry::make('pdf_path')
                                    ->label(__('Generated PDF'))
                                    ->placeholder(__('No PDF generated yet'))
                                    ->formatStateUsing(fn ($record) => $record->pdf_path ? __('View/Download PDF') : __('No PDF generated yet'))
                                    ->badge()
                                    ->color(fn ($record) => $record->pdf_path ? 'success' : 'gray')
                                    ->icon('heroicon-o-document')
                                    ->url(fn ($record) => $record->pdf_path ? asset($record->pdf_path . '/' . $record->pdf_file_name) : null, shouldOpenInNewTab: true)
                                    ->visible(fn ($record) => $record->pdf_path || $record->pdf_file_name),

                                Infolists\Components\SpatieTagsEntry::make('tags')
                                    ->type('internships')
                                    ->visible(fn ($record) => $record->tags->isNotEmpty()),
                            ]),

                        Infolists\Components\Section::make(__('Important Dates'))
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->schema([
                                Infolists\Components\TextEntry::make('announced_at')
                                    ->icon('heroicon-o-megaphone')
                                    ->date()
                                    ->visible(fn ($record) => $record->announced_at),

                                Infolists\Components\TextEntry::make('validated_at')
                                    ->icon('heroicon-o-check-circle')
                                    ->date()
                                    ->visible(fn ($record) => $record->validated_at),

                                Infolists\Components\TextEntry::make('received_at')
                                    ->icon('heroicon-o-inbox')
                                    ->date()
                                    ->visible(fn ($record) => $record->received_at),

                                Infolists\Components\TextEntry::make('signed_at')
                                    ->icon('heroicon-o-pencil-square')
                                    ->date()
                                    ->visible(fn ($record) => $record->signed_at),
                            ]),

                        // Only show if cancelled
                        Infolists\Components\Section::make(__('Cancellation Details'))
                            ->icon('heroicon-o-x-circle')
                            ->collapsible()
                            ->visible(fn ($record) => $record->appliedCancellation())
                            ->schema([
                                Infolists\Components\TextEntry::make('cancellation_reason')
                                    ->icon('heroicon-o-exclamation-triangle'),
                                Infolists\Components\TextEntry::make('verification_document_url')
                                    ->label(__('Verification Document'))
                                    ->url(fn ($record) => Storage::disk('cancellation_verification')->url($record->verification_document_url))
                                    ->openUrlInNewTab(),
                            ]),
                    ]),
            ]);
    }
}
