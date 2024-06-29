<?php

namespace App\Livewire;

use App\Enums;
use App\Models\InternshipOffer;
use App\Services\Filament\Forms\AddOrganizationForm;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class NewInternship extends Page implements HasForms
{
    use InteractsWithForms;

    protected ?string $heading = 'Custom Page Heading';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->model(InternshipOffer::class)
            ->schema([

                Forms\Components\Section::make(__('Publish Internship Offer'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Fieldset::make('Organization Information')
                            ->columns(3)
                            ->columnSpan(1)

                            ->schema([
                                // Forms\Components\TextInput::make('year_id')
                                //     ->numeric(),
                                // ...(new AddOrganizationForm())->getSchema(),
                                // Forms\Components\Select::make('organization_id')
                                //     // ->hiddenOn('edit')
                                //     // ->default($this->organization_id)
                                //     ->relationship('organization', 'name')
                                //     ->searchable()
                                //     ->preload()
                                //     ->required()
                                //     // ->live()
                                //     // ->createOptionAction(
                                //     //     fn (Action $action) => $action->modalWidth('3xl'),
                                //     // )
                                //     ->id('country_id')
                                //     ->createOptionForm([
                                //         Forms\Components\TextInput::make('name')
                                //             ->required(),
                                //         Forms\Components\TextInput::make('city')
                                //             ->required(),
                                //         Country::make('country')
                                //             ->required()
                                //             ->searchable(),
                                //         Forms\Components\TextInput::make('address'),
                                //     ])->editOptionForm([
                                //         Forms\Components\TextInput::make('name')
                                //             ->required(),
                                //         Forms\Components\TextInput::make('city')
                                //             ->required(),
                                //         Country::make('country')
                                //             ->required()
                                //             ->searchable(),
                                //         Forms\Components\TextInput::make('address'),
                                //     ]),

                                Forms\Components\TextInput::make('organization_name')
                                    ->maxLength(191),
                                Country::make('country')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\ToggleButtons::make('organization_type')
                                    ->options([
                                        'Company' => __('Company'),
                                        'NGO' => __('NGO'),
                                        'Public' => __('Public Institution'),
                                    ])
                                    ->columnSpan(2)
                                    ->inline()
                                    ->default('Company')
                                    ->live(),

                                Forms\Components\Fieldset::make('Organization Responsible Information')
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
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\ToggleButtons::make('internship_type')
                                    ->inline()
                                    ->options([
                                        'Remote' => __('Remote'),
                                        'OnSite' => __('On site'),
                                    ])
                                    ->default('Remote')
                                    ->live(),
                                Forms\Components\Fieldset::make('Internship theme')
                                    ->columns(4)
                                    ->schema([
                                        Forms\Components\Textarea::make('project_title')
                                            ->columnSpanFull(),
                                        Forms\Components\MarkdownEditor::make('project_details')
                                            ->columnSpanFull(),

                                        Forms\Components\TagsInput::make('tags')
                                            ->placeholder('Add tags')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Fieldset::make('Internship location and duration')
                                    ->columns(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('internship_location')
                                            ->columnSpan(2)
                                            ->maxLength(191),
                                        Forms\Components\TextInput::make('internship_duration')
                                            ->label('Duration in months')
                                            ->minValue(1)
                                            ->maxValue(6)
                                            ->inputMode('decimal')
                                            ->numeric(),
                                    ]),
                            ]),
                        // Forms\Components\TextInput::make('status'),
                        // Forms\Components\Toggle::make('is_active'),
                        Forms\Components\Fieldset::make('Recruting Information')
                            ->columns(3)
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\ToggleButtons::make('recruting_type')
                                    ->inline()
                                    ->options([
                                        'SchoolManaged' => __('School Managed'),
                                        'RecruiterManaged' => __('Recruiter Managed'),
                                    ])
                                    ->default('RecruiterManaged')
                                    ->live(),
                                Forms\Components\Fieldset::make(__('Internship application details'))
                                    ->columns(8)
                                    ->schema([

                                        Forms\Components\TextInput::make('application_link')
                                            ->url()
                                            ->suffixIcon('heroicon-m-globe-alt')
                                            ->hidden(fn (Get $get) => $get('recruting_type') != 'RecruiterManaged')
                                            ->columnSpan(2)
                                            ->maxLength(191),
                                        Forms\Components\TextInput::make('application_email')
                                            ->columnSpan(2)
                                            ->hidden(fn (Get $get) => $get('recruting_type') != 'RecruiterManaged')
                                            ->email()
                                            ->maxLength(191),

                                        Forms\Components\Select::make('currency')
                                            ->hidden(fn (Get $get) => $get('recruting_type') != 'SchoolManaged')
                                            ->default(Enums\Currency::MDH->getSymbol())
                                            ->options([
                                                Enums\Currency::EUR->value => Enums\Currency::EUR->getSymbol(),
                                                Enums\Currency::USD->value => Enums\Currency::USD->getSymbol(),
                                                Enums\Currency::MDH->value => Enums\Currency::MDH->getSymbol(),
                                            ])
                                            ->live()
                                            ->id('currency'),
                                        Forms\Components\TextInput::make('remuneration')
                                            ->columnSpan(2)
                                            ->hidden(fn (Get $get) => $get('recruting_type') != 'SchoolManaged')
                                            ->label('Monthly remuneration')
                                            ->numeric()
                                    // get prefix from crrency value
                                            ->id('remuneration')
                                            ->prefix(fn (Get $get) => $get('currency'))
                                            ->live(),

                                        Forms\Components\TextInput::make('workload')
                                            ->placeholder('Hours / Week')
                                            ->numeric()
                                            ->visible(fn (Get $get): bool => $get('remuneration') !== null && $get('remuneration') > 0),

                                        Forms\Components\DatePicker::make('expire_at')
                                            ->columnSpan(2)
                                            ->label('Application deadline'),
                                        Forms\Components\FileUpload::make('attached_file')
                                            ->columnSpanFull(),
                                    ]),

                            ]),
                    ]),
            ]);

    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = InternshipOffer::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title(__('Internship Offer has been submitted successfully'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.new-internship'); //->layout('components.layouts.public');
    }
}
