<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneInputNumberType;
use App\Models\Student;

class StudentAccountSettingsPage extends MyProfileComponent
{
    // public function render()
    // {
    //     return view('livewire.student-account-settings-page');
    // }

    protected string $view = 'livewire.student-account-settings-page';

    public array $data;

    public Student $user;

    public array $only = ['email_perso', 'phone', 'lm', 'cv'];

    public function mount()
    {
        $this->user = auth()->user();
        // $this->user = Filament::getCurrentPanel()->auth()->user();

        $this->form->fill($this->user->only($this->only));


    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email_perso')->email()->label(__('Personal Email')),
                PhoneInput::make('phone'),
                TextInput::make('lm')->label(__('Cover Letter'))
                    ->placeholder(__('Link to your cover letter')),
                TextInput::make('cv')->label(__('CV'))
                    ->placeholder(__('Link to your CV')),

            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        // $data = collect($this->validate($this->user->rules())->form->getState())->only($this->only)->all();
        $data = collect($this->form->getState())->only($this->only)->all();

        // $data = $this->validate($data, [
        //     'email_perso' => ['required', 'email'],
        //     'phone' => ['required'],
        //     'lm' => ['required'],
        //     'cv' => ['required'],
        // ]);

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
