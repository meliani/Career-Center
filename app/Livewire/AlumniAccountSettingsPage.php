<?php

namespace App\Livewire;

use App\Enums;
use App\Models\StudentExchangePartner;
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
    public static $sort = 2;

    protected string $view = 'livewire.student-account-settings-page';

    public array $data;

    public $user;

    public array $only = ['degree', 'program', 'abroad_school', 'graduation_year_id', 'work_status'];

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
                        // $this->getNameComponent(),
                        // Forms\Components\TextInput::make('name')
                        //     ->label(__('Name')),
                        // Forms\Components\TextInput::make('email')
                        //     ->label(__('Email')),
                        // TextInput::make('phone_number')
                        //     ->label(__('Phone')),

                        Forms\Components\Select::make('graduation_year_id')
                            ->label(__('Year of Graduation from INPT'))
                            ->options(\App\Models\Year::all()->pluck('title', 'id')->toArray()),

                        Forms\Components\ToggleButtons::make('degree')
                            ->label(__('Please let us know about your degree upgrade'))
                            ->options(Enums\AlumniDegree::class)
                            ->inline()
                            ->live(),
                        Forms\Components\ToggleButtons::make('work_status')
                            ->label(__('Could you kindly tell us about your current work status?'))
                            ->options(Enums\WorkStatus::class)
                            ->inline()
                            ->live(),
                        // Forms\Components\Select::make('program')
                        //     ->options(Enums\Program::class),
                        // Forms\Components\Select::make('abroad_school')
                        //     ->options(fn () => StudentExchangePartner::all()->pluck('name', 'id')->toArray()),
                    ])
                    ->columns(1),

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
            'degree' => $data['degree'],
            // 'program' => $data['program'],
            // 'abroad_school' => $data['abroad_school'],
            'graduation_year_id' => $data['graduation_year_id'],
            'work_status' => $data['work_status'],
        ]);

        \Filament\Notifications\Notification::make()
            ->title(__('Profile updated'))
            ->success()
            ->send();
    }
}
