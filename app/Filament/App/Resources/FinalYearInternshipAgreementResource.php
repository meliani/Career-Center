<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Enums\Currency;
use App\Filament\Actions\Action\ApplyForCancelInternshipAction;
use App\Filament\Actions\Action\Processing\GenerateInternshipAgreementAction;
use App\Filament\App\Resources\FinalYearInternshipAgreementResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class FinalYearInternshipAgreementResource extends StudentBaseResource
{
    protected static ?string $model = FinalYearInternshipAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Internship Agreement';

    protected static ?string $pluralModelLabel = 'Internship Agreements';

    protected static ?string $navigationGroup = 'Final Project';

    public static function canAccess(): bool
    {
        if (Auth::user() instanceof Student) {
            if (Auth::user()->level === Enums\StudentLevel::ThirdYear) {
                return true;
            }
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    /*     public static function form(Form $form): Form
        {
            // $organization_id = null;
            // $organization = null;

            return $form
                ->schema([
                    Forms\Components\Wizard::make([

                        Forms\Components\Wizard\Step::make('Company representative & Supervisors')
                            ->icon('heroicon-o-users')
                            ->description(__('Select or create the organization representatives'))
                            ->schema([

                                Forms\Components\Grid::make(2)
                                    ->schema([

                                        Forms\Components\Section::make('External Supervisor')
                                            ->description('The supervisor from the academic institution')
                                            ->schema([
                                                Forms\Components\Select::make('external_supervisor_id')
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
                                                        Forms\Components\TextInput::make('first_name')
                                                            ->required()
                                                            ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                        Forms\Components\TextInput::make('last_name')
                                                            ->required()
                                                            ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                        Forms\Components\TextInput::make('email')
                                                            ->email()
                                                            ->required()
                                                            ->unique('apprenticeship_agreement_contacts', 'email'),
                                                        Forms\Components\TextInput::make('phone')->tel(),
                                                        Forms\Components\TextInput::make('function')->required(),
                                                        Forms\Components\TextInput::make('organization_id')
                                                            ->default(fn (Get $get) => $get('../organization_id')),
                                                        Forms\Components\Placeholder::make('organization_name')
                                                            ->label('')
                                                            // ->content(fn (Get $get) => $get('../../organization_id') ?? 'No organization selected'),
                                                            ->content(fn (Get $get) => dd($get)),

                                                    ]),
                                            ]),

                                        Forms\Components\Section::make('Internal Supervisor')
                                            ->description('The supervisor from the academic institution')
                                            ->schema([
                                                    Forms\Components\Select::make('internal_supervisor_id')
                                                    ->preload()
                                                    ->relationship(
                                                        name: 'internalSupervisor',
                                                        titleAttribute: 'name',
                                                    )
                                                    ->getOptionLabelFromRecordUsing(
                                                        fn (Model $record) => "{$record->name}"
                                                    )
                                                    ->searchable(),
                                                ]),
                                    ]),
                            ]),

                        // Step 4: Internship Details
                        Forms\Components\Wizard\Step::make('Internship Details')
                            ->icon('heroicon-o-document-text')
                            ->description('Define the internship specifics')
                            ->schema([
                                    Forms\Components\Section::make('Basic Information')
                                        ->schema([
                                            Forms\Components\TextInput::make('title')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpanFull(),

                                            DateRangePicker::make('internship_period')
                                                ->required()
                                                ->columnSpanFull(),

                                            Forms\Components\RichEditor::make('description')
                                                ->required()
                                                ->columnSpanFull(),

                                            Forms\Components\TextInput::make('office_location')
                                                ->label('Office Location (if different from organization address)')
                                                ->maxLength(255)
                                                ->columnSpanFull(),
                                        ]),

                                    Forms\Components\Section::make('Terms')
                                        ->schema([
                                            Forms\Components\Grid::make(3)
                                                ->schema([
                                                    Forms\Components\Select::make('currency')
                                                    // ->options(Currency::class)
                                                    ->options([
                                                        Enums\Currency::EUR->value => Enums\Currency::EUR->getSymbol(),
                                                        Enums\Currency::USD->value => Enums\Currency::USD->getSymbol(),
                                                        Enums\Currency::MDH->value => Enums\Currency::MDH->getSymbol(),
                                                    ])
                                                    ->required()
                                                    ->live(),

                                                    Forms\Components\TextInput::make('remuneration')
                                                    ->label('Monthly Remuneration')
                                                    ->numeric()
                                                    ->prefix(fn (Get $get) => Currency::tryFrom($get('currency'))?->getSymbol())
                                                    ->required(),

                                                    Forms\Components\TextInput::make('workload')
                                                    ->label('Weekly Hours')
                                                    ->numeric()
                                                    ->suffix('hours')
                                                    ->required(),
                                                ]),
                                        ]),

                                    Forms\Components\Section::make('Keywords')
                                        ->schema([
                                            SpatieTagsInput::make('tags')
                                                // ->suggestion()
                                                ->type('FinalYearInternship-' . Year::current())
                                                ->splitKeys(['Tab', ',', ' '])
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                    ])
                        // ->skippable()
                        ->persistStepInQueryString()
                        ->startOnStep(1)
                        ->columnSpanFull()
                        ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE))),
                    // ->submitAction(
                    //     \Filament\Actions\Action::make('create')
                    //         ->label('Create Internship')
                    //         ->icon('heroicon-o-check')
                    // ),
                    // ->submitAction(
                    //     \Filament\Actions\Action::make('create')
                    //         ->label('Create Internship')
                    //         ->icon('heroicon-o-check')
                    // ),
                ]);
        } */

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__(''))
            ->recordTitleAttribute('title')
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create your first internship agreement')
                    ->url(route('filament.app.resources.final-year-internship-agreements.create'))
                    ->icon('heroicon-o-plus-circle')
                    ->button(),
            ])
            ->contentGrid(
                [
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                    '2xl' => 1,
                ]
            )
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('title')
                        ->searchable()
                        ->sortable()
                        ->label('Title')
                        ->toggleable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('organization.name')
                        ->searchable()
                        ->sortable()
                        ->label('Organization')
                        ->toggleable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('internship_period')
                        ->searchable()
                        ->sortable()
                        ->label('Internship Period')
                        ->toggleable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('status')
                        ->searchable()
                        ->sortable()
                        ->label('Status')
                        ->toggleable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('created_at')
                        ->searchable()
                        ->sortable()
                        ->label('Created At')
                        ->toggleable(false)
                        ->sortable(false),

                ]),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([

                    ApplyForCancelInternshipAction::make('Apply for internship cancellation')
                        ->color('danger')
                        ->icon('heroicon-o-bolt-slash'),
                    Tables\Actions\ViewAction::make()
                        ->color('success')
                        ->label('View details'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->label('Edit possible fields'),
                ])
                    ->dropdownPlacement('top-start')
                    ->dropdownWidth(\Filament\Support\Enums\MaxWidth::Small)
                    // ->outlined()
                    ->label(__('Manage my internship agreement'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('primary'),
                GenerateInternshipAgreementAction::make('Generate Agreement PDF')
                    ->label(__('Generate Agreement PDF'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->outlined()
                    ->size(\Filament\Support\Enums\ActionSize::ExtraSmall)
                    ->button(),

            ], position: Tables\Enums\ActionsPosition::AfterColumns)
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Internship agreement and validation process'))
                    ->schema([

                        Infolists\Components\Fieldset::make('Internship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('organization.name')

                                    ->label('Organization'),
                                Infolists\Components\TextEntry::make('internship_period')
                                    ->label('Internship Period'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('created_at')

                                    ->label('Created At'),
                            ]),
                        Infolists\Components\Fieldset::make('Validation process')
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
                Infolists\Components\Section::make(__('Internship agreement paperwork'))
                    ->schema([
                        Infolists\Components\TextEntry::make('agreement_pdf_url')
                            ->label('Agreement PDF')
                            ->placeholder(__('No PDF generated yet'))
                            ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? _('Download agreement PDF') : _('No PDF generated yet'))
                            ->columnSpan(3)
                            ->badge()
                            ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true)
                            ->hintActions(
                                [Infolists\Components\Actions\Action::make('generate')
                                    ->label('Generate PDF')
                                    ->icon('heroicon-o-document-plus')
                                    ->button(),

                                ]
                            ),
                        // ->suffixAction(
                        //     Infolists\Components\Actions\Action::make('delete')
                        //         ->label('Delete PDF')
                        //         ->icon('heroicon-o-trash'),
                        // ),

                    ]),
            ]);

    }
}
