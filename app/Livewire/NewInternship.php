<?php

namespace App\Livewire;

use App\Models\InternshipOffer;
use App\Services\Filament\Forms\AddOrganizationForm;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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
                    ->columns(4)
                    ->schema([
                        // Forms\Components\TextInput::make('year_id')
                        //     ->numeric(),
                        // ...(new AddOrganizationForm())->getSchema(),
                        Forms\Components\Select::make('organization_id')
                            // ->hiddenOn('edit')
                            // ->default($this->organization_id)
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            // ->live()
                            // ->createOptionAction(
                            //     fn (Action $action) => $action->modalWidth('3xl'),
                            // )
                            ->id('country_id')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('city')
                                    ->required(),
                                Country::make('country')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('address'),
                            ])->editOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('city')
                                    ->required(),
                                Country::make('country')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('address'),
                            ]),

                        // Forms\Components\TextInput::make('organization_name')
                        //     ->maxLength(191),
                        Country::make('country')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('internship_type'),
                        Forms\Components\TextInput::make('responsible_fullname')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('responsible_occupation')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('responsible_phone')
                            ->tel()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('responsible_email')
                            ->email()
                            ->maxLength(191),
                        Forms\Components\Fieldset::make('Internship Details')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Textarea::make('project_title')
                                    ->columnSpanFull(),
                                Forms\Components\MarkdownEditor::make('project_details')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('internship_location')
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('keywords')
                                    ->maxLength(191),
                                Forms\Components\FileUpload::make('attached_file'),
                                Forms\Components\Textarea::make('link')
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('internship_duration'),
                                Forms\Components\TextInput::make('remuneration')
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('currency')
                                    ->maxLength(10),
                                Forms\Components\TextInput::make('recruting_type'),
                                Forms\Components\TextInput::make('application_email')
                                    ->email()
                                    ->maxLength(191),
                                Forms\Components\TextInput::make('status'),
                                Forms\Components\TextInput::make('applyable')
                                    ->maxLength(1),
                                Forms\Components\DatePicker::make('expire_at'),
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
