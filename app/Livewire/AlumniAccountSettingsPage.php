<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class AlumniAccountSettingsPage extends MyProfileComponent
{
    // public function render()
    // {
    //     return view('livewire.student-account-settings-page');
    // }

    protected string $view = 'livewire.student-account-settings-page';

    public array $data;

    public $user;

    public array $only = ['phone_number', 'degree', 'program', 'abroad_school', 'graduation_year_id'];

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
                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\ToggleButtons::make('degree')
                            ->options([
                                'bachelor' => 'Bachelor',
                                'master' => 'Master',
                                'phd' => 'PhD',
                            ])
                            ->inline()
                            ->live(),
                        Forms\Components\Select::make('program')
                            ->options([
                                'engineering' => 'Engineering',
                                'business' => 'Business',
                                'design' => 'Design',
                                'science' => 'Science',
                                'other' => 'Other',
                            ]),
                        Forms\Components\Select::make('abroad_school')
                            ->options([
                                'Telecom Paris' => 'Telecom Paris',
                                'Telecom SudParis' => 'Telecom SudParis',
                                'Telecom Lille' => 'Telecom Lille',
                                'Telecom Nancy' => 'Telecom Nancy',
                                'Telecom Saint-Etienne' => 'Telecom Saint-Etienne',
                                'Telecom Bretagne' => 'Telecom Bretagne',
                            ]),
                        Forms\Components\Select::make('graduation_year_id')
                            ->label(__('Graduation year'))
                            ->options(\App\Models\Year::all()->pluck('title', 'id')->toArray()),
                        TextInput::make('phone_number')
                            ->label(__('Phone')),
                    ])
                    ->columns(2),

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
            'phone_number' => $data['phone_number'],
            'degree' => $data['degree'],
            'program' => $data['program'],
            'abroad_school' => $data['abroad_school'],
            'graduation_year_id' => $data['graduation_year_id'],
        ]);

        \Filament\Notifications\Notification::make()
            ->title(__('Profile updated'))
            ->success()
            ->send();
    }
}
