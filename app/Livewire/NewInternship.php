<?php

namespace App\Livewire;

use App\Enums;
use App\Models\InternshipOffer;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class NewInternship extends Page implements HasForms
{
    use InteractsWithForms;

    protected ?string $heading = 'New Internship Offer';

    public InternshipOffer $internshipOffer;

    public array $data = [];

    public bool $confirming = false;

    public bool $submitted = false;

    public function mount(): void
    {
        $this->internshipOffer = new InternshipOffer;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->model(InternshipOffer::class)
            ->columns(4)
            ->schema([

                Forms\Components\Fieldset::make('Organization Information')
                    ->columns(3)
                    // ->grow()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('organization_name')
                            ->maxLength(191)
                            ->autocomplete('organization')
                            ->columnSpan(2),
                        Country::make('country')
                            ->searchable()
                            ->default('MA'),

                        Forms\Components\ToggleButtons::make('organization_type')
                            ->options(Enums\OrganizationType::class)
                            ->columnSpan(2)
                            ->inline()
                            ->default('Company')
                            ->live(),

                        Forms\Components\Fieldset::make('Organization Responsible Information')
                            ->label('Organization Responsible Information (reserved for school administration)')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('responsible_name')
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('responsible_occupation')
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('responsible_phone')
                                    ->tel()
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('responsible_email')
                                    ->email()
                                    ->maxLength(191),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Internship Details')
                    ->columns(3)
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\ToggleButtons::make('internship_level')
                            ->inline()
                            ->options(Enums\InternshipLevel::class)
                            ->default('FinalYearInternship')
                            ->columnSpan(2)
                            ->live(),
                        Forms\Components\ToggleButtons::make('internship_type')
                            ->label('Internship type (for location)')
                            ->inline()
                            ->options(Enums\InternshipType::class)
                            ->default('OnSite')
                            ->live(),
                        Forms\Components\Fieldset::make('Internship theme')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Textarea::make('project_title')
                                    ->columnSpanFull(),
                                Forms\Components\MarkdownEditor::make('project_details')
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('expertise_field_id')
                                    ->relationship('expertiseField', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->columnSpan(2),

                                // Forms\Components\TagsInput::make('tags')
                                //     ->placeholder(__('Add keywords'))
                                //     ->splitKeys(['Tab', ',', ';'])
                                //     ->columnSpan(2),
                                Forms\Components\SpatieTagsInput::make('tags')
                                    ->placeholder(__('Add keywords'))
                                    ->splitKeys(['Tab', ',', ';'])
                                    ->columnSpan(2)
                                    ->type('InternshipOffer')
                                    ->suggestions(
                                        function () {
                                            return \Spatie\Tags\Tag::withType('InternshipOffer')->pluck('name');
                                        },
                                    ),

                            ]),
                        Forms\Components\Fieldset::make('Internship location and duration')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('internship_location')
                                    ->columnSpan(2)
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('internship_duration')
                                    ->label('Duration in months')
                                    ->minValue(fn (Get $get) => $get('internship_level') == 'FinalYearInternship' ? 4 : 1)
                                    ->default(4)
                                    ->maxValue(6)
                                    ->inputMode('decimal')
                                    ->numeric(),

                            ]),
                        Forms\Components\TextInput::make('number_of_students_requested')
                            ->label('Number of students requested')
                            ->minValue(1)
                            ->default(1)
                            ->maxValue(20)
                            ->inputMode('decimal')
                            ->columnSpanFull()
                            ->numeric(),
                    ]),
                // Forms\Components\TextInput::make('status'),
                // Forms\Components\Toggle::make('is_active'),
                Forms\Components\Fieldset::make('Internship application details')
                    ->columns(4)
                    ->columnSpan(4)
                    ->schema([
                        Forms\Components\ToggleButtons::make('recruiting_type')
                            ->label('')
                            ->inline()
                            ->options(Enums\RecruitingType::class)
                            ->default('RecruiterManaged')
                            ->live(),
                        Forms\Components\Group::make()
                            ->columns(1)
                            ->schema([
                                Forms\Components\DatePicker::make('expire_at')
                                    // ->columnSpan(2)
                                    ->default(now()->addMonth())
                                    ->label('Application deadline'),
                                Forms\Components\TextInput::make('application_link')
                                    ->helperText(__('Leave empty if not applicable'))
                                    // ->url()
                                    ->dehydrateStateUsing(function ($state) {
                                        if ($state && ! preg_match('/^https?:\/\//', $state)) {
                                            return 'https://' . $state;
                                        }

                                        return $state;
                                    })
                                    ->suffixIcon('heroicon-m-globe-alt')
                                    ->hidden(fn (Get $get) => $get('recruiting_type') != 'RecruiterManaged')
                                    // ->columnSpan(2)
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('application_email')
                                    ->helperText(__('Leave empty if not applicable'))
                                    // ->columnSpan(2)
                                    ->hidden(fn (Get $get) => $get('recruiting_type') != 'RecruiterManaged')
                                    ->email()
                                    ->maxLength(191),

                                Forms\Components\TextInput::make('remuneration')
                                    // ->columnSpan(3)
                                    ->hidden(fn (Get $get) => $get('recruiting_type') != 'SchoolManaged')
                                    ->label('Monthly remuneration')
                                    ->numeric()
                                    // get prefix from crrency value
                                    ->id('remuneration')
                                    ->prefix(fn (Get $get) => $get('currency'))
                                    ->helperText(__('Leave empty if not applicable'))
                                    ->live(),
                                Forms\Components\ToggleButtons::make('currency')
                                    ->hidden(fn (Get $get) => (($get('recruiting_type') != 'SchoolManaged') || ($get('remuneration') <= 0)))
                                    ->default(null)
                                    ->options([
                                        Enums\Currency::EUR->value => Enums\Currency::EUR->getSymbol(),
                                        Enums\Currency::USD->value => Enums\Currency::USD->getSymbol(),
                                        Enums\Currency::MDH->value => Enums\Currency::MDH->getSymbol(),
                                        /* Enums\Currency::EUR->getSymbol() => Enums\Currency::EUR->value,
                                        Enums\Currency::USD->getSymbol() => Enums\Currency::USD->value,
                                        Enums\Currency::MDH->getSymbol() => Enums\Currency::MDH->value, */
                                    ])
                                    // ->options(Enums\Currency::class)
                                    ->inline()
                                    ->live()
                                    ->id('currency'),
                                Forms\Components\TextInput::make('workload')
                                    // ->columnSpan(2)
                                    ->placeholder('Hours / Week')
                                    ->helperText(__('Leave empty if not applicable'))
                                    ->numeric()
                                    ->visible(fn (Get $get): bool => $get('remuneration') !== null && $get('remuneration') > 0),

                                // Forms\Components\FileUpload::make('attached_file')
                                //     ->columnSpanFull(),
                            ]),

                    ]),
            ]);

    }

    public function confirm(): void
    {
        // $this->data = $this->form->getState();

        $this->internshipOffer->fill($this->form->getState());

        $this->confirming = true;
    }

    public function create(): void
    {
        $this->internshipOffer->fill($this->form->getState());
        $this->internshipOffer->year_id = Year::current()->id;
        $this->internshipOffer->status = 'Submitted';
        $this->internshipOffer->save();
        $this->form->model($this->internshipOffer)->saveRelationships();
        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->internshipOffer = new InternshipOffer;
        $this->confirming = false;
        $this->submitted = false;
        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.new-internship', [
            'confirming' => $this->confirming,
            'submitted' => $this->submitted,
            'internshipOffer' => $this->internshipOffer,
            // 'data' => $this->data,
        ]);
    }
}
