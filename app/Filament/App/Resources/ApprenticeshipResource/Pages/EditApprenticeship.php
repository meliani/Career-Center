<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Enums;
use App\Enums\Currency;
use App\Filament\App\Resources\ApprenticeshipResource;
use App\Models\Organization;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class EditApprenticeship extends EditRecord
{
    use EditRecord\Concerns\HasWizard;
    
    protected static string $resource = ApprenticeshipResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        // Check if the record is in a status that allows editing
        if ($this->record->status !== Enums\Status::Draft) {
            Notification::make()
                ->title('This apprenticeship agreement cannot be edited')
                ->body('Only agreements in Draft status can be edited.')
                ->warning()
                ->send();
                
            redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Create a formatted internship_period for the DateRangePicker from starting_at and ending_at
        if (isset($data['starting_at']) && isset($data['ending_at'])) {
            // Format dates to match the model's format
            $startDate = \Carbon\Carbon::parse($data['starting_at'])->format('d/m/Y');
            $endDate = \Carbon\Carbon::parse($data['ending_at'])->format('d/m/Y');
            $data['internship_period'] = $startDate . ' - ' . $endDate;
        }
        
        return $data;
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('View details'))
                ->icon('heroicon-o-eye')
                ->color('success'),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Apprenticeship agreement updated')
            ->body('Your apprenticeship agreement has been updated successfully.');
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSteps(): array
    {
        return [
            // Step 1: Organization
            Forms\Components\Wizard\Step::make('Organization')
                ->label(__('Organization'))
                ->icon('heroicon-o-building-office')
                ->description(__('Organization information'))
                ->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Select::make('organization_id')
                                ->label('Organization')
                                ->helperText(__('Organization that will host your apprenticeship'))
                                ->relationship('organization', 'name', fn (Builder $query) => $query->active())
                                ->searchable()
                                ->preload()
                                ->getOptionLabelUsing(fn (Model $record) => $record->name . ' - ' . $record->country)
                                ->required()
                                ->live()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $organization = Organization::find($state);
                                    $set('organization_info', $organization ? $organization->toArray() : null);
                                })
                                ->afterStateHydrated(function ($state, callable $set) {
                                    $organization = Organization::find($state);
                                    $set('organization_info', $organization ? $organization->toArray() : null);
                                })
                                ->disabled(),
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
                            Forms\Components\Placeholder::make('organization_note')
                                ->content('Note: Organization information cannot be changed after creation. If you need to change the organization, please create a new apprenticeship agreement.')
                                ->extraAttributes(['class' => 'text-warning-600']),
                        ]),
                ]),

            // Step 2: Organization Contacts
            Forms\Components\Wizard\Step::make('Organization representative & Supervisors')
                ->label(__('Organization representative & Supervisors'))
                ->icon('heroicon-o-users')
                ->description(__('Contact information'))
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
                                        ->disabled(),
                                    
                                    Forms\Components\Placeholder::make('parrain_note')
                                        ->content('Note: Organization representative cannot be changed after creation. If needed, please contact your advisor.')
                                        ->extraAttributes(['class' => 'text-warning-600']),
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
                                        ->disabled(),
                                        
                                    Forms\Components\Placeholder::make('supervisor_note')
                                        ->content('Note: Supervisor cannot be changed after creation. If needed, please contact your advisor.')
                                        ->extraAttributes(['class' => 'text-warning-600']),
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
                                ->helperText(__('A brief title describing your apprenticeship'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            DateRangePicker::make('internship_period')
                                ->label(__('Apprenticeship Period'))
                                // ->displayFormat('d/m/Y')
                                // ->format('d/m/Y')
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (!empty($state)) {
                                        $dates = explode(' - ', $state);
                                        if (count($dates) === 2) {
                                            // Check if the period is valid (max 8 weeks)
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
                                            } else {
                                                // We store these dates for direct database saving
                                                $set('starting_at', \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d'));
                                                $set('ending_at', \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d'));
                                            }
                                        }
                                    }
                                })
                                ->required()
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
                            SpatieTagsInput::make('tags')
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
                                ->helperText(__('Draft status will allow you to continue editing. Announced will submit for approval.'))
                                ->required(),
                        ]),
                ]),
        ];
    }
}
