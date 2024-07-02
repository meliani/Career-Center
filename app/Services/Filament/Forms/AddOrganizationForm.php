<?php

namespace App\Services\Filament\Forms;

use Filament\Forms;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class AddOrganizationForm
{
    protected int | \Closure | null $organization_id;

    // public function __construct(\Closure | int | null $organization_id = null)
    // {
    //     $this->organization_id = $organization_id;
    //     if ($organization_id instanceof \Closure) {
    //         dump($organization_id);
    //     }
    // }

    public function getSchema(): array
    {
        return [
            Forms\Components\Select::make('organization_id')
                ->label('Organization name')
                ->optionsLimit(3)
                ->hiddenOn('edit')
                ->relationship('organization', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->id('organization_id')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Organization name')
                        ->required(),
                    Forms\Components\TextInput::make('city')
                        ->required(),
                    Country::make('country')
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('address'),
                ]),
        ];
    }
}
