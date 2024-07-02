<?php

namespace App\Services\Filament\Forms;

use App\Enums;
use App\Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ApprenticeshipAgreementForm
{
    public function getSchema(): array
    {
        return [
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Placeholder::make('Note')->hiddenLabel()->content(__('Notice: You can only announce one internship agreement during an academic year.')),
                    Forms\Components\Placeholder::make('Note')->hiddenLabel()->content(__('When you save this form, you will not be able to change the organization and its representatives.')),
                ]),
            // ...(new AddOrganizationForm())->getSchema(),
            Forms\Components\Section::make()
                ->columnSpan(1)
                ->schema([
                    // Forms\Components\Select::make('organization_id')
                    //     ->optionsLimit(3)
                    //     ->hiddenOn('edit')
                    //     ->relationship('organization', 'name')
                    //     ->searchable()
                    //     ->preload()
                    //     ->required()
                    //     ->live()
                    //     ->id('organization_id')
                    //     ->createOptionForm([
                    //         Forms\Components\TextInput::make('name')
                    //             ->label('Organization name')
                    //             ->required(),
                    //         Forms\Components\TextInput::make('city')
                    //             ->required(),
                    //         \Parfaitementweb\FilamentCountryField\Forms\Components\Country::make('country')
                    //             ->required()
                    //             ->searchable(),
                    //         Forms\Components\TextInput::make('address'),
                    //     ]),
                    ...(new AddOrganizationForm())->getSchema(),
                    Forms\Components\Fieldset::make(__('Organization contacts'))
                        ->columnSpan(1)
                        ->columns(2)
                        ->schema([
                            Forms\Components\Section::make()
                                ->schema([Forms\Components\Placeholder::make('Note')->hiddenLabel()
                                    ->content(__('Parrain is the representative of the host organization'))])
                                ->hiddenOn('edit'),
                            Forms\Components\Select::make('parrain_id')
                                ->hiddenOn('edit')
                                ->relationship(
                                    name: 'parrain',
                                    titleAttribute: 'name',
                                    // ignoreRecord: true,
                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} - {$record->function}") // {$record->organization->name} :
                                ->searchable(['first_name', 'last_name'])
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\Select::make('organization_id')
                                        ->relationship('organization', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->default(fn (Get $get) => $get('organization_id')),
                                    Forms\Components\Fieldset::make(__('Parrain'))
                                        ->columns(3)
                                        ->schema([
                                            ...(new AddOrganizationContactForm())->getSchema(),
                                        ]),
                                ]),
                            Forms\Components\Select::make('supervisor_id')
                                ->relationship(
                                    name: 'supervisor',
                                    titleAttribute: 'name',
                                    // ignoreRecord: true,
                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('organization_id', $get('organization_id'))
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} - {$record->function}")
                                ->searchable(['first_name', 'last_name'])
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\Select::make('organization_id')
                                        // ->default(fn (Get $get) => $get('organization_id'))
                                        ->relationship('organization', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Forms\Components\Fieldset::make(__('Supervisor'))
                                        ->columns(3)
                                        ->schema([
                                            ...(new AddOrganizationContactForm())->getSchema(),
                                        ]),
                                ]),
                            DateRangePicker::make('internship_period')
                                ->required()
                                ->hiddenOn('edit')
                                ->columnSpan(2),
                            // Forms\Components\DateTimePicker::make('starting_at'),
                            // Forms\Components\DateTimePicker::make('ending_at'),
                        ]),

                ]),
            Forms\Components\Section::make(__('Internship information'))
                ->columnSpan(1)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->columnSpanFull()->required(),

                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull(),
                    Forms\Components\SpatieTagsInput::make('keywords')
                        ->splitKeys(['Tab', ',', ';'])
                        ->nestedRecursiveRules([
                            'min:2',
                            'max:50',
                        ])
                        ->placeholder('Add a keyword and press enter or click away to add it')
                        ->color('success')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('office_location')
                        ->label(__('Internship Office location if different than organization address.'))
                        ->maxLength(255)
                        ->columnSpan(2),
                ]),

            Forms\Components\Fieldset::make(__('Remuneration and workload'))
                ->columnSpan(1)
                ->columns(4)
                ->schema([
                    Forms\Components\Select::make('currency')
                        ->default(Enums\Currency::MDH->getSymbol())
                        ->options([
                            Enums\Currency::EUR->value => Enums\Currency::EUR->getSymbol(),
                            Enums\Currency::USD->value => Enums\Currency::USD->getSymbol(),
                            Enums\Currency::MDH->value => Enums\Currency::MDH->getSymbol(),
                        ])
                        ->live()
                        ->id('currency'),
                    Forms\Components\TextInput::make('remuneration')
                        ->label('Monthly remuneration')
                        ->numeric()
                        ->columnSpan(2)
                        // get prefix from crrency value
                        ->id('remuneration')
                        ->prefix(fn (Get $get) => $get('currency'))
                        ->live(),

                    Forms\Components\TextInput::make('workload')
                        ->placeholder('Hours / Week')
                        ->numeric()
                        ->visible(fn (Get $get): bool => $get('remuneration') !== null && $get('remuneration') > 0),
                ]),
            Forms\Components\Placeholder::make('Note')
                ->content(__('To generate document save and go to apprenticeship list')),
            // Section::make()->schema([ Placeholder::make('No Label')->hiddenLabel()->content("blah blah") ]),

            // Forms\Components\Fieldset::make(__('Internship documents'))
            //     // ->columns(6)
            //     ->schema([
            //         \Filament\Forms\Components\Actions::make([
            //             Actions\Action\Processing\GenerateApprenticeshipAgreementPdfAction::make('Generate Apprenticeship Agreement PDF')
            //                 ->label(__('Generate Apprenticeship Agreement PDF'))
            //                 ->requiresConfirmation(),
            //         ]),
            //     ]),
        ];
    }
}
