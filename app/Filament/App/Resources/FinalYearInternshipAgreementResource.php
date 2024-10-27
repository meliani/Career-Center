<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Enums\Currency;
use App\Filament\App\Resources\FinalYearInternshipAgreementResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        if (env('APP_ENV') === 'production') {
            return false;
        }
        if (Auth::user() instanceof Student) {
            if (Auth::user()->level === Enums\StudentLevel::ThirdYear) {
                return false;
            }
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // Step 1: Initial Information
                    // Forms\Components\Wizard\Step::make('Basic Information')
                    //     ->icon('heroicon-o-information-circle')
                    //     ->description('Start with the basic details')
                    //     ->schema([

                    //         /*                             Forms\Components\Section::make('Academic Details')
                    //             ->schema([
                    //                 Forms\Components\Select::make('academic_year_id')
                    //                     ->relationship('year', 'title')
                    //                     ->required()
                    //                     ->searchable()
                    //                     ->preload(),

                    //                 Forms\Components\Select::make('student_id')
                    //                     ->relationship(
                    //                         name: 'student',
                    //                         titleAttribute: 'full_name',
                    //                         modifyQueryUsing: fn (Builder $query) => $query->with('user')
                    //                     )
                    //                     ->getOptionLabelFromRecordUsing(
                    //                         fn (Model $record) => "{$record->user->full_name} ({$record->student_number})"
                    //                     )
                    //                     ->searchable(['first_name', 'last_name', 'student_number'])
                    //                     ->preload()
                    //                     ->required(),
                    //             ]), */
                    //     ]),

                    // Step 2: Organization Selection
                    Forms\Components\Wizard\Step::make('Organization')
                        ->icon('heroicon-o-building-office')
                        ->description('Select or create an organization')
                        ->schema([
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('notice')
                                        ->content('Notice: You can only announce one internship agreement during an academic year.')
                                        ->extraAttributes(['class' => 'text-warning-600']),
                                    Forms\Components\Placeholder::make('warning')
                                        ->content('When you save this form, you will not be able to change the organization and its representatives.')
                                        ->extraAttributes(['class' => 'text-warning-600']),
                                ])
                                ->collapsible(),
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Select::make('organization_id')
                                        ->label('Select Existing Organization or Create New')
                                        ->relationship('ActiveOrganizations', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelUsing(fn (Model $record) => $record->name . ' - ' . $record->country)
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->label('Organization Name')
                                                ->live()
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    $set('slug', str($state)->slug());
                                                }),
                                            Forms\Components\TextInput::make('slug')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->maxLength(255)
                                                ->unique(Organization::class, 'slug', ignoreRecord: true),
                                            Forms\Components\TextInput::make('website')
                                                ->url()
                                                ->maxLength(255),
                                            \Parfaitementweb\FilamentCountryField\Forms\Components\Country::make('country')
                                                ->default('MA')
                                                ->required()
                                                ->live()
                                                ->searchable(),
                                            Forms\Components\TextInput::make('city')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('address')
                                                ->maxLength(255),
                                        ])
                                        ->required()
                                        ->live()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $organization = Organization::find($state);
                                            $set('organization_info', $organization ? $organization->toArray() : null);
                                        }),
                                    Forms\Components\Placeholder::make('organization_info')
                                        ->label('Organization Information')
                                        ->content(function ($get) {
                                            $organization = Organization::find($get('organization_id'));

                                            if (! $organization) {
                                                return __('Select an organization or create a new one.');
                                            }

                                            return __('Adress') . ': ' . e($organization->address) . ' - ' .
                                                e($organization->city) . ', ' .
                                                e($organization->country);
                                        }),
                                ]),
                            // we gonna add a section with live information about the organization when its selected

                        ]),

                    // Step 3: Organization Contacts
                    Forms\Components\Wizard\Step::make('Supervision')
                        ->icon('heroicon-o-users')
                        ->description('Add supervisors and mentors')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Section::make('Company Mentor')
                                        ->description('The mentor from the organization who will guide the intern')
                                        ->schema([
                                            Forms\Components\Select::make('parrain_id')
                                                ->preload()
                                                ->relationship(
                                                    name: 'parrain',
                                                    titleAttribute: 'full_name',
                                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                                                )
                                                ->getOptionLabelFromRecordUsing(
                                                    fn (Model $record) => "{$record->full_name} - {$record->function}"
                                                )
                                                ->searchable(['first_name', 'last_name'])
                                                ->required()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('first_name')->required(),
                                                    Forms\Components\TextInput::make('last_name')->required(),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required()
                                                        ->unique('apprenticeship_agreement_contacts', 'email'),
                                                    Forms\Components\TextInput::make('phone')->tel(),
                                                    Forms\Components\TextInput::make('function')->required(),
                                                    Forms\Components\Hidden::make('organization_id')
                                                        ->default(fn (Get $get) => $get('../../organization_id')),
                                                ]),
                                        ]),

                                    Forms\Components\Section::make('Academic Supervisor')
                                        ->description('The supervisor from the academic institution')
                                        ->schema([
                                            Forms\Components\Select::make('external_supervisor_id')
                                                ->preload()
                                                ->relationship(
                                                    name: 'supervisor',
                                                    titleAttribute: 'full_name',
                                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                                                )
                                                ->getOptionLabelFromRecordUsing(
                                                    fn (Model $record) => "{$record->full_name} - {$record->function}"
                                                )
                                                ->searchable(['first_name', 'last_name'])
                                                ->required()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('first_name')->required(),
                                                    Forms\Components\TextInput::make('last_name')->required(),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required()
                                                        ->unique('organization_contacts', 'email'),
                                                    Forms\Components\TextInput::make('phone')->tel(),
                                                    Forms\Components\TextInput::make('function')->required(),
                                                    Forms\Components\Hidden::make('organization_id')
                                                        ->default(fn (Get $get) => $get('../../organization_id')),
                                                ]),
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
                    ->columnSpanFull(),
                // ->submitAction(
                //     \Filament\Actions\Action::make('create')
                //         ->label('Create Internship')
                //         ->icon('heroicon-o-check')
                // ),
            ]);
    }
    /*     public static function form(Form $form): Form
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
        } */

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
                SpatieTagsColumn::make('tags')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
