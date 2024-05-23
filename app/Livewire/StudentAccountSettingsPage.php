<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class StudentAccountSettingsPage extends MyProfileComponent
{
    // public function render()
    // {
    //     return view('livewire.student-account-settings-page');
    // }

    protected string $view = 'livewire.student-account-settings-page';

    public array $data;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email_perso')->email()->label(__('Personal Email')),
                TextInput::make('phone')->label(__('Phone')),
                TextInput::make('lm')->label(__('Cover Letter'))
                    ->placeholder(__('Link to your cover letter')),
                TextInput::make('cv')->label(__('CV'))
                    ->placeholder(__('Link to your CV')),

            ])
            ->statePath('data');
    }
}
