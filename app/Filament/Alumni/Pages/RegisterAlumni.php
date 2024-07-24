<?php

namespace App\Filament\Alumni\Pages;

use App\Enums;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class RegisterAlumni extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('title')
                            ->options(Enums\Title::class)
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        // TextInput::make('first_name')
                        //     ->required()
                        //     ->maxLength(255),
                        // TextInput::make('last_name')
                        //     ->required()
                        //     ->maxLength(255),
                        $this->getNameFormComponent()
                            ->Placeholder(__('Full Name'))
                            ->columnSpanFull(),

                    ]),
                $this->getEmailFormComponent()
                    ->label('Email')
                    ->Placeholder('email@email.com'),
                // ->endsWith(['@ine.inpt.ac.ma']),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                // ToggleButtons::make('degree')
                //     ->options(Enums\AlumniDegree::class)
                //     ->inline()
                //     ->required(),
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        Select::make('program')
                            ->options(Enums\Program::class)
                            ->required(),
                        Select::make('graduation_year_id')
                            ->label(__('Graduation year'))
                            ->options(\App\Models\Year::all()->pluck('title', 'id')->toArray()),
                    ]),
                // TextInput::make('email_perso')
                //     ->email()
                //     ->required()
                //     ->maxLength(255),
                TextInput::make('phone_number')
                    ->required()
                    ->Placeholder('06XXXXXXXX')
                    ->rule('phone'),

            ]);
    }
}
