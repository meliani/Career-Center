<?php

namespace App\Services\Filament\Forms;

use Filament\Forms;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class AddOrganizationForm
{
    public static function getSchema(): array
    {
        return [
            Forms\Components\Select::make('organization_id')
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
                ]),
        ];
    }
}
