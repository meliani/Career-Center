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
    public static function getSchema(): array
    {
        return [
            ...AddOrganizationForm::getSchema(),
            Forms\Components\TextInput::make('title')
                ->columnSpanFull()->required(),
            Forms\Components\RichEditor::make('description')
                ->columnSpanFull(),
            Forms\Components\SpatieTagsInput::make('keywords'),

            Forms\Components\Fieldset::make(__('Organization contacts'))
                ->columns(4)
                ->schema([
                    Forms\Components\Select::make('parrain_id')
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
                                ->createOptionForm([
                                    ...AddOrganizationForm::getSchema(),
                                ]),
                            Forms\Components\Fieldset::make(__('Parrain'))
                                ->columns(3)
                                ->schema([
                                    Forms\Components\Section::make()
                                        ->schema([Forms\Components\Placeholder::make('Note')->hiddenLabel()
                                            ->content("Le parrain est le représentant de l'organisme d'accueil")]),
                                    ...AddOrganizationContactForm::getSchema(),
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
                                ->relationship('organization', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    ...AddOrganizationForm::getSchema(),
                                ]),
                            Forms\Components\Fieldset::make(__('Supervisor'))
                                ->columns(3)
                                ->schema([
                                    ...AddOrganizationContactForm::getSchema(),
                                ]),
                        ]),
                ]),

            Forms\Components\Fieldset::make(__('Internship dates'))
                ->columns(4)
                ->schema([
                    DateRangePicker::make('internship_period')
                        ->required(),
                    // Forms\Components\DateTimePicker::make('starting_at'),
                    // Forms\Components\DateTimePicker::make('ending_at'),
                ]),
            Forms\Components\Fieldset::make(__('Remuneration and workload'))
                ->columns(8)
                ->schema([
                    Forms\Components\Select::make('currency')
                        ->default(Enums\Currency::MDH->getSymbol())
                        ->options([
                            Enums\Currency::EUR->getSymbol() => Enums\Currency::EUR->getSymbol(),
                            Enums\Currency::USD->getSymbol() => Enums\Currency::USD->getSymbol(),
                            Enums\Currency::MDH->getSymbol() => Enums\Currency::MDH->getSymbol(),
                        ])
                        ->live()
                        ->id('currency'),
                    Forms\Components\TextInput::make('remuneration')
                        ->numeric()
                        ->columnSpan(2)
                        // get prefix from crrency value
                        ->id('remuneration')
                        ->prefix(fn (Get $get) => $get('currency')),

                    Forms\Components\TextInput::make('workload')
                        ->placeholder('Hours / Week')
                        ->numeric(),
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
