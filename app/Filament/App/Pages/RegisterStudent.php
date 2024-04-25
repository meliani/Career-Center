<?php

namespace App\Filament\App\Pages;

use App\Enums;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class RegisterStudent extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make(__('Your name'))
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('title')
                            ->options(Enums\Title::class)
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                    ]),
                // $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                ToggleButtons::make('level')
                    ->options(Enums\StudentLevel::class)
                    ->required(),
                Select::make('program')
                    ->options(Enums\Program::class)
                    ->required(),
                TextInput::make('email_perso')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(255),

            ]);
    }
}
