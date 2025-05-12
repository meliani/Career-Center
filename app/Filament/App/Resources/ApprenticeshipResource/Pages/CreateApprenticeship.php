<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Enums;
use App\Filament\App\Resources\ApprenticeshipResource;
use App\Models\Apprenticeship;
use App\Models\Organization;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Filament;
class CreateApprenticeship extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    
    protected static string $resource = ApprenticeshipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Apprenticeship agreement created')
            ->body('Your apprenticeship agreement has been created successfully. You can now view and edit it.');
    }

    protected function getSteps(): array
    {
        return [
            // Step 1: Organization
            Forms\Components\Wizard\Step::make('Organization')
                ->label(__('Organization'))
                ->icon('heroicon-o-building-office')
                ->description(__('Select or create an organization'))
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('notice')
                                ->content('Notice: You can only create one apprenticeship agreement during an academic year.')
                                ->extraAttributes(['class' => 'text-warning-600']),
                            Forms\Components\Placeholder::make('warning')
                                ->content('When you save this form, you will not be able to change the organization and its representatives.')
                                ->extraAttributes(['class' => 'text-warning-600']),
                        ])
                        ->collapsible(),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Select::make('organization_id')
                                ->label('Organization')
                                ->helperText(__('Select an existing organization or create a new one'))
                                ->hint(__('The organization that will host your apprenticeship'))
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
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('website')
                                                ->placeholder('Enter website URL')
                                                ->reactive()
                                                ->rules([
                                                    'nullable',
                                                    'regex:/^((https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:?#[\]@!$&\'()*+,;=]*)?)$/i',
                                                ])
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
                                                ->helperText(__('This will be visible on the apprenticeship agreement'))
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
                ]),

            // Step 2: Organization Contacts
            Forms\Components\Wizard\Step::make('Organization representative & Supervisors')
                ->label(__('Organization representative & Supervisors'))
                ->icon('heroicon-o-users')
                ->description(__('Select or create the organization representatives'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Section::make(__('Organization Representative'))
                                ->label(__('Organization Representative'))
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
                                                        ->required()
                                                        ->options(Enums\Title::class),
                                                    Forms\Components\TextInput::make('first_name')->required(),
                                                    Forms\Components\TextInput::make('last_name')->required(),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required(),
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

                            Forms\Components\Section::make(__('Supervisor'))
                                ->label(__('Supervisor'))
                                ->description(__('Your apprenticeship supervisor from the organization'))
                                ->schema([
                                    Forms\Components\Select::make('supervisor_id')
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
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Select::make('title')
                                                        ->required()
                                                        ->options(Enums\Title::class),
                                                    Forms\Components\TextInput::make('first_name')
                                                        ->required()
                                                        ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                    Forms\Components\TextInput::make('last_name')
                                                        ->required()
                                                        ->formatStateUsing(fn (?string $state): ?string => ucwords($state)),
                                                    Forms\Components\TextInput::make('email')
                                                        ->email()
                                                        ->required(),
                                                    Forms\Components\TextInput::make('phone')->tel()->required(),
                                                    Forms\Components\TextInput::make('function')->required(),
                                                ]),
                                        ])
                                        ->createOptionUsing(function ($data, Get $get) {
                                            $supervisor = new \App\Models\InternshipAgreementContact;
                                            $supervisor->fill($data);
                                            $supervisor->role = Enums\OrganizationContactRole::Mentor;
                                            $supervisor->organization_id = $get('organization_id');
                                            $supervisor->save();

                                            return $supervisor->getKey();
                                        }),
                                ]),
                        ]),
                ]),

            // Step 3: Apprenticeship Details
            Forms\Components\Wizard\Step::make(__('Apprenticeship Details'))
                ->icon('heroicon-o-document-text')
                ->description(__('Define your apprenticeship specifics'))
                ->schema([
                    Forms\Components\Section::make('Basic Information')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label(__('Apprenticeship Title'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            DateRangePicker::make('internship_period')
                                ->label(__('Apprenticeship Period'))
                                ->required()
                                ->afterStateHydrated(function ($component, $state, $get, $set) {
                                    if (!empty($state)) {
                                        $dates = explode(' - ', $state);
                                        if (count($dates) === 2) {
                                            $set('starting_at', $dates[0]);
                                            $set('ending_at', $dates[1]);
                                        }
                                    }
                                })
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    if (!empty($state)) {
                                        $dates = explode(' - ', $state);
                                        if (count($dates) === 2) {
                                            $start = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0]);
                                            $end = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1]);
                                            $weeks = ceil($start->floatDiffInRealWeeks($end));
                                            if ($weeks > 8) {
                                                $set('internship_period', null); 
                                                Filament\Notifications\Notification::make()
                                                    ->title('Internship period too long')
                                                    ->body('The internship period cannot exceed 8 weeks.')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }
                                    }
                                })
                                ->helperText(__('The internship period cannot exceed 8 weeks'))
                                ->columnSpanFull(),

                            // Forms\Components\Select::make('internship_level')
                            //     ->label(__('Apprenticeship Type'))
                            //     ->options([
                            //         \App\Enums\InternshipLevel::IntroductoryInternship->value => __('First Year'), 
                            //         \App\Enums\InternshipLevel::TechnicalInternship->value => __('Second Year'),
                            //         \App\Enums\InternshipLevel::FinalYearInternship->value => __('Final Year'),
                            //     ])
                            //     ->required(),

                            Forms\Components\MarkdownEditor::make('description')
                                ->label(__('Description of Tasks & Responsibilities'))
                                ->helperText(__('Describe the tasks and responsibilities you will have during your apprenticeship'))
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('office_location')
                                ->label(__('Office Location (if different from organization address)'))
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Section::make('Remuneration and Workload')
                        ->schema([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\Select::make('currency')
                                        ->label(__('Currency'))
                                        ->options([
                                            Enums\Currency::EUR->value => Enums\Currency::EUR->getSymbol(),
                                            Enums\Currency::USD->value => Enums\Currency::USD->getSymbol(),
                                            Enums\Currency::MDH->value => Enums\Currency::MDH->getSymbol(),
                                        ])
                                        ->live(),

                                    Forms\Components\TextInput::make('remuneration')
                                        ->label(__('Monthly Remuneration'))
                                        ->helperText(__('Leave empty if no remuneration'))
                                        ->numeric()
                                        ->prefix(fn (Get $get) => ($currency = $get('currency')) !== null ? Enums\Currency::tryFrom($currency)?->getSymbol() : ''),
                                        
                                    Forms\Components\TextInput::make('workload')
                                        ->label(__('Weekly Hours'))
                                        ->helperText(__('Number of hours per week'))
                                        ->numeric()
                                        ->suffix('hours'),
                                        
                                    Forms\Components\Select::make('internship_type')
                                        ->label(__('Work Modality'))
                                        ->helperText(__('How you will work during the apprenticeship'))
                                        ->options(Enums\InternshipType::class)
                                        ->required(),
                                ]),
                        ]),

                    Forms\Components\Section::make('Keywords')
                        ->schema([
                            SpatieTagsInput::make('keywords')
                                ->label(__('Keywords'))
                                ->helperText(__('Add keywords that describe your apprenticeship'))
                                ->splitKeys(['Tab', ',', ' '])
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Section::make('Status')
                        ->schema([
                            Forms\Components\ToggleButtons::make('status')
                                ->inline()
                                ->options([
                                    Enums\Status::Announced->value => Enums\Status::Announced->getLabel(),
                                    Enums\Status::Draft->value => Enums\Status::Draft->getLabel(),
                                ])
                                ->helperText(__('Draft status will allow you to save and edit later. Announced will submit for approval.'))
                                ->default(Enums\Status::Draft->value)
                                ->required(),
                        ]),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Apprenticeship
    {
        try {
            return static::getModel()::create($data);
        } catch (QueryException $e) {
            // Check if it's a unique constraint violation
            if (str_contains($e->getMessage(), 'student_id')) {
                Notification::make()
                    ->title('You already have an apprenticeship agreement for this academic year')
                    ->body('Only one apprenticeship agreement is allowed per academic year.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            throw $e;
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
