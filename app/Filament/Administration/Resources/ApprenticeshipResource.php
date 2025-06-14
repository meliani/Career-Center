<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\ApprenticeshipResource\Pages;
use App\Filament\Administration\Resources\ApprenticeshipResource\RelationManagers;
use App\Filament\Core\BaseResource;
use App\Models\Apprenticeship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use App\Models\Year;
class ApprenticeshipResource extends BaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationBadgeTooltip = 'Announced apprenticeships';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?string $modelLabel = 'Apprenticeship';

    protected static ?string $pluralModelLabel = 'Apprenticeships';

    protected static ?string $title = 'Announced apprenticeships';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count('id');
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator();
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make(__('Important Dates'))
                            ->description(__('Key dates for the apprenticeship process'))
                            ->icon('heroicon-o-calendar')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\DateTimePicker::make('validated_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('received_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('signed_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                            ]),

                        Forms\Components\Tabs::make('Apprenticeship')
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
                                                    ->relationship('organization', 'name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('office_location')
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('internship_type')
                                                    ->options(Enums\InternshipType::class)
                                                    ->required(),
                                            ])->columns(3),

                                        Forms\Components\Section::make(__('Apprenticeship Details'))
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                // Forms\Components\TagsInput::make('keywords')
                                                //     ->columnSpanFull(),
                                                Forms\Components\MarkdownEditor::make('description')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('Supervisors'))
                                    ->icon('heroicon-o-users')
                                    ->schema([
                                        Forms\Components\Section::make(__('External Supervisor'))
                                            ->description(__('The student\'s supervisor from the organization'))
                                            ->schema([
                                                Forms\Components\Select::make('supervisor_id')
                                                    ->label(__('External Supervisor'))
                                                    ->preload()
                                                    ->relationship(
                                                        name: 'supervisor',
                                                        titleAttribute: 'full_name',
                                                        modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query->where('organization_id', $get('organization_id'))
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
                                                    ->createOptionUsing(function ($data, Forms\Get $get) {
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

                                        Forms\Components\Section::make(__('Organization Representative'))
                                            ->description(__('The organization\'s main contact person'))
                                            ->schema([
                                                Forms\Components\Select::make('parrain_id')
                                                    ->label(__('Organization Representative'))
                                                    ->preload()
                                                    ->relationship(
                                                        name: 'parrain',
                                                        titleAttribute: 'full_name',
                                                        modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query->where('organization_id', $get('organization_id'))
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
                                                    ->createOptionUsing(function ($data, Forms\Get $get) {
                                                        $contact = new \App\Models\InternshipAgreementContact;
                                                        $contact->fill($data);
                                                        $contact->role = Enums\OrganizationContactRole::Representative;
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
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->description(fn ($record) => $record->student?->id_pfe),

                Tables\Columns\TextColumn::make('student.program')
                    ->label('Program')
                    ->tooltip(fn ($record) => $record->student?->program->getDescription()),

                Tables\Columns\TextColumn::make('student.level')
                    ->label('Level')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state?->getLabel()),

                Tables\Columns\TextColumn::make('organization.name')
                    ->description(fn ($record) => $record->organization->city . ', ' . $record->organization->country)
                    ->searchable(),

                Tables\Columns\TextColumn::make('starting_at')
                    ->label('Date Début')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ending_at')
                    ->label('Date Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remuneration')
                    ->label('Rémunération')
                    ->money(fn ($record) => $record->currency?->value ?? 'MDH')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->description(fn ($record) => __('Start date') . ': ' . $record->starting_at->format('d/m/Y') . ' - ' . __('End date') . ': ' . $record->ending_at->format('d/m/Y'))
                    ->limit(50),

                Tables\Columns\TextColumn::make('parrain.full_name')
                    ->searchable(false)
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.full_name')
                    ->searchable(false)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('internship_type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->groups([
                Tables\Grouping\Group::make('student.level')
                    ->label('Level')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('student.level')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('internship_type')
                    ->options(Enums\InternshipType::class)
                    ->label('Work Modality'),
                Tables\Filters\SelectFilter::make('student_level')
                    ->label('Level')
                    ->options([
                        'FirstYear' => __('FirstYear'),
                        'SecondYear' => __('SecondYear'),
                        // 'ThirdYear' => __('ThirdYear'),
                        // 'MasterIoTBigData' => __('MasterIoTBigData'),
                        // 'AlumniTransitional' => __('AlumniTransitional'),
                        // 'Alumni' => __('Alumni'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value) => $query->whereHas('student', fn ($q) => $q->where('level', $value))
                        );
                    }),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(Year::getYearsForSelect(1))
                    ->default(Year::current()->id)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value) => $query->where('year_id', $value)
                        );
                    })
                    ->indicateUsing(fn (array $data): ?string => $data['value'] ? __('Year') . ': ' . Year::find($data['value'])->title : null)
                    ->visible(fn () => auth()->user()->isAdministrator() === true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
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
            RelationManagers\AmendmentsRelationManager::class,
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
        $record = $infolist->getRecord();
        $verification_document_url = $record->verification_document_url 
            ? Storage::disk('cancellation_verification')->url($record->verification_document_url)
            : null;

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
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('student.program')
                                            ->label(__('Program'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('organization.name')
                                            ->label(__('Organization'))
                                            ->icon('heroicon-o-building-office')
                                            ->badge()
                                            ->color('success'),
                                    ]),

                                Infolists\Components\Section::make(__('Apprenticeship Details'))
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('title')
                                            ->markdown(),
                                        Infolists\Components\TextEntry::make('description')
                                            ->markdown(),
                                        // Infolists\Components\SpatieTagsEntry::make('keywords'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Location & Schedule'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([

                                Infolists\Components\Section::make(__('Location & Work Modality'))
                                    ->icon('heroicon-o-map-pin')
                                    ->columns(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('office_location')
                                            ->icon('heroicon-o-map-pin')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('internship_type')
                                            ->icon('heroicon-o-building-office-2')
                                            ->badge(),
                                    ]),

                                Infolists\Components\Section::make(__('Schedule'))
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('starting_at')
                                            ->icon('heroicon-o-calendar')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('ending_at')
                                            ->icon('heroicon-o-calendar')
                                            ->badge(),
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
                                    ->badge(),
                                Infolists\Components\TextEntry::make('agreement_pdf_url')
                                    ->label(__('Agreement PDF'))
                                    ->placeholder(__('No PDF generated yet'))
                                    ->formatStateUsing(fn ($record) => $record->pdf_path ? __('View/Download PDF') : __('No PDF generated yet'))
                                    ->badge()
                                    ->color(fn ($record) => $record->pdf_path ? 'success' : 'gray')
                                    ->icon('heroicon-o-document')
                                    ->url(fn ($record) => $record->pdf_path ? asset($record->pdf_path . '/' . $record->pdf_file_name) : null, shouldOpenInNewTab: true)
                                    ->visible(fn ($record) => $record->pdf_path || $record->pdf_file_name),
                            ]),

                        Infolists\Components\Section::make(__('Important Dates'))
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->schema([
                                Infolists\Components\TextEntry::make('validated_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('received_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('signed_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                            ]),

                        Infolists\Components\Section::make(__('Cancellation Information'))
                            ->icon('heroicon-o-x-circle')
                            ->collapsible()
                            ->collapsed()
                            ->visible(fn ($record) => $record->cancelled_at !== null || $record->cancellation_reason !== null)
                            ->schema([
                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->label(__('Cancelled At'))
                                    ->icon('heroicon-o-calendar')
                                    ->badge()
                                    ->color('danger')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('cancellation_reason')
                                    ->label(__('Cancellation Reason'))
                                    ->icon('heroicon-o-document-text')
                                    ->markdown()
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('verification_document_url')
                                    ->label(__('Verification Document'))
                                    ->icon('heroicon-o-document')
                                    ->formatStateUsing(fn ($state) => $state ? __('View Document') : __('No document'))
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->url(fn ($record) => $verification_document_url, shouldOpenInNewTab: true)
                                    ->visible(fn ($record) => $record->verification_document_url !== null),
                            ]),
                    ]),
            ]);
    }
}
