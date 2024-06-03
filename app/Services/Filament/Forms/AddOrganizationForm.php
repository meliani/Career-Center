<?php

namespace App\Services\Filament\Forms;

use Filament\Forms;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class AddOrganizationForm
{
    protected int | \Closure | null $organization_id;

    public function __construct(\Closure | int | null $organization_id = null)
    {
        $this->organization_id = $organization_id;
        if ($organization_id instanceof \Closure) {
            dump($organization_id);
        }
    }

    public function getSchema(): array
    {
        return [
            Forms\Components\Select::make('organization_id')
                ->hiddenOn('edit')
                ->default($this->organization_id)
                ->relationship('organization', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->id('country_id')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('city')
                        ->required(),
                    Country::make('country')
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('address')
                ]),
        ];
    }
}
