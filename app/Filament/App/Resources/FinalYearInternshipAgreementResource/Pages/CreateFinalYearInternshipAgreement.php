<?php

namespace App\Filament\App\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Enums;
use App\Enums\Currency;
use App\Filament\App\Resources\FinalYearInternshipAgreementResource;
use App\Models\Organization;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

use function Filament\Support\is_app_url;

class CreateFinalYearInternshipAgreement extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = FinalYearInternshipAgreementResource::class;

    protected function getSteps(): array
    {
        return [
            // Step 1: Organization
            Forms\Components\Wizard\Step::make('Organization')
                ->label(__('Organization'))
                ->icon('heroicon-o-building-office')
                ->description(__('Select or create an organization'))
                ->schema([
                    /* Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('notice')
                                ->content('Notice: You can only announce one internship agreement during an academic year.')
                                ->extraAttributes(['class' => 'text-warning-600']),
                            Forms\Components\Placeholder::make('warning')
                                ->content('When you save this form, you will not be able to change the organization and its representatives.')
                                ->extraAttributes(['class' => 'text-warning-600']),
                        ])
                        ->collapsible(), */
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Select::make('organization_id')
                                ->label('Organization')
                                ->helperText(__('Select an existing organization or create a new one'))
                                ->hint(__('The organization that will host the internship'))
                                ->relationship('organization', 'name', fn (Builder $query) => $query->active())
                                ->searchable()
                                ->preload()
                                ->getOptionLabelUsing(fn (Model $record) => $record->name . ' - ' . $record->country)
                                ->createOptionForm([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->label('Organization Name')
                                                ->live()
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    $set('slug', str($state)->slug());
                                                })
                                                ->debounce(300),
                                            Forms\Components\TextInput::make('slug')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->maxLength(255)
                                                ->unique(Organization::class, 'slug', ignoreRecord: true),
                                            Forms\Components\TextInput::make('website')
                                                ->placeholder('Enter website URL')
                                                ->reactive()
                                                ->rules([
                                                    'nullable',
                                                    'regex:/^((https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:?#[\]@!$&\'()*+,;=]*)?)$/i',
                                                ])
                                                // ->activeUrl()
                                                ->beforeStateDehydrated(function (?string $state, callable $set) {
                                                    if (empty($state)) {
                                                        return;
                                                    }

                                                    if (! Str::startsWith($state, ['http://', 'https://'])) {
                                                        $state = 'https://' . $state;
                                                        $set('website', $state);
                                                    }
                                                }),
                                            \Parfaitementweb\FilamentCountryField\Forms\Components\Country::make('country')
                                                ->default('MA')
                                                ->required()
                                                ->live()
                                                ->searchable(),
                                            Forms\Components\TextInput::make('city')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('address')
                                                ->helperText(__('This will be visible on the internship agreement'))
                                                ->required()
                                                ->maxLength(255),
                                        ]),
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
                                    if ($get('organization_id')) {
                                        $info = $get('organization_info');
                                        $htmlContent = '
                                            <div class="organization-info">
                                                <p><strong>Organization Name:</strong> ' . e($info['name']) . '</p>
                                                <p><strong>Country:</strong> ' . e($info['country']) . '</p>
                                                <p><strong>City:</strong> ' . e($info['city']) . '</p>
                                                <p><strong>Address:</strong> ' . e($info['address']) . '</p>
                                                <p><strong>Website:</strong> <a href="' . e($info['website']) . '" target="_blank">' . e($info['website']) . '</a></p>
                                            </div>
                                        ';

                                        return new HtmlString($htmlContent);
                                    } else {
                                        return __('Please select an organization');
                                    }
                                }),
                        ]),
                    // we gonna add a section with live information about the organization when its selected

                ]),
            // ->beforeValidation(function (Component $livewire) {
            //     $data = $livewire->form->getState();
            //     // dd($data);
            // })
            // ->afterValidation(function () {
            //     $data = $this->form->getState();

            //     $this->record = new ($this->getModel())($data);

            //     $this->record->save();
            // }),

            // Step 3: Organization Contacts
            Forms\Components\Wizard\Step::make('Organization representative & Supervisors')
                ->label(__('Organization representative & Supervisors'))
                ->icon('heroicon-o-users')
                ->description(__('Select or create the organization representatives'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Section::make('Organization Representative')
                                ->label('Organization Representative')
                                ->description(__('The person who will sign the agreement on behalf of the organization'))
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
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Select::make('title')
                                                        ->options(Enums\Title::class),
                                                    Forms\Components\TextInput::make('first_name')->required(),
                                                    Forms\Components\TextInput::make('last_name')->required(),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required()
                                                        ->unique('internship_agreement_contacts', 'email'),
                                                    Forms\Components\TextInput::make('phone')->tel()->required(),
                                                    Forms\Components\TextInput::make('function')->required(),
                                                ]),
                                        ])
                                        ->createOptionUsing(function ($data, Get $get) {

                                            $parrain = new \App\Models\InternshipAgreementContact;
                                            $parrain->fill($data);
                                            $parrain->role = Enums\OrganizationContactRole::Parrain;
                                            $parrain->organization_id = $get('organization_id');
                                            $parrain->save();

                                            return $parrain->getKey();
                                        }),
                                ]),

                            Forms\Components\Section::make('External Supervisor')
                                ->label(__('External Supervisor'))
                                ->description(__('Your internship supervisor from the organization'))
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
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Select::make('title')
                                                        ->options(Enums\Title::class),

                                                    Forms\Components\TextInput::make('first_name')
                                                        ->required()
                                                        ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                    Forms\Components\TextInput::make('last_name')
                                                        ->required()
                                                        ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required()
                                                        ->unique('internship_agreement_contacts', 'email'),
                                                    Forms\Components\TextInput::make('phone')->tel()->required(),
                                                    Forms\Components\TextInput::make('function')->required(),
                                                ]),
                                        ])
                                        ->createOptionUsing(function ($data, Get $get) {

                                            $parrain = new \App\Models\InternshipAgreementContact;
                                            $parrain->fill($data);
                                            $parrain->role = Enums\OrganizationContactRole::Mentor;
                                            $parrain->organization_id = $get('organization_id');
                                            $parrain->save();

                                            return $parrain->getKey();
                                        }),
                                ]),

                            Forms\Components\Section::make('Internal Supervisor')
                                ->description('The supervisor from the academic institution')
                                ->schema([
                                    Forms\Components\Select::make('internal_supervisor_id')
                                        ->hint(__('You can suggest a supervisor from the academic institution'))
                                        ->preload()
                                        ->relationship(
                                            name: 'internalSupervisor',
                                            titleAttribute: 'full_name',
                                            // modifyQueryUsing: fn (Builder $query) => $query->select('first_name', 'last_name', 'department')->where('role', Enums\Role::Professor)
                                        )
                                        ->getOptionLabelFromRecordUsing(
                                            fn (Model $record) => "{$record->first_name} {$record->last_name}\n" . __('Department') . ": {$record->department->getDescription()}"
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

                            Forms\Components\MarkdownEditor::make('description')
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
                                        ->live(),

                                    Forms\Components\TextInput::make('remuneration')
                                        ->label('Monthly Remuneration')
                                        ->numeric()
                                        ->prefix(fn (Get $get) => Currency::tryFrom($get('currency'))?->getSymbol()),

                                    Forms\Components\TextInput::make('workload')
                                        ->label('Weekly Hours')
                                        ->numeric()
                                        ->suffix('hours'),
                                ]),
                        ]),

                    Forms\Components\Section::make('Keywords')
                        ->schema([
                            SpatieTagsInput::make('tags')
                                // ->suggestion()
                                ->type('internships')
                                ->splitKeys(['Tab', ',', ' '])
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    // public function create(bool $another = false): void
    // {
    //     $data = $this->form->getState();

    //     $this->record->update($data);

    //     $redirectUrl = $this->getRedirectUrl();

    //     $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
    // }
}
