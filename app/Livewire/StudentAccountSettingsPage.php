<?php

namespace App\Livewire;

use Filament\Facades\Filament;
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

    public $user;

    public array $only = ['email_perso', 'phone', 'lm', 'cv'];

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        // dd($this->user);
        $this->form->fill($this->user->only($this->only));

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email_perso')->email()->label(__('Personal Email')),
                TextInput::make('phone')->label(__('Phone'))
                    ->numeric(),
                TextInput::make('lm')->label(__('Cover Letter'))
                    ->placeholder(__('Link to your cover letter')),
                TextInput::make('cv')->label(__('CV'))
                    ->placeholder(__('Link to your CV')),

            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        // $this->validate();

        /* $data = collect($this->form->getState())->only('new_password')->all();
        $this->user->update([
            'password' => Hash::make($data['new_password']),
        ]); */

        $this->user->update([
            'email_perso' => $data['email_perso'],
            'phone' => $data['phone'],
            'lm' => $data['lm'],
            'cv' => $data['cv'],
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Your profile has been updated successfully')
            ->success()
            ->send();
    }
}
