<?php

namespace App\Services\Filament\Forms;

use App\Enums;
use Filament\Forms;

class AddOrganizationContactForm
{
    public function getSchema(): array
    {
        return [
            Forms\Components\Select::make('title')
                ->options(Enums\Title::class)
                ->required(),
            Forms\Components\TextInput::make('first_name')
                ->required(),
            Forms\Components\TextInput::make('last_name')
                ->required(),
            Forms\Components\TextInput::make('function')
                ->required(),
            Forms\Components\TextInput::make('phone')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->required(),
            Forms\Components\Select::make('role')
                ->options(Enums\OrganizationContactRole::class)
                ->required(),
        ];
    }
}
