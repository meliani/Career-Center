<?php

namespace App\Livewire;

use Filament\Forms\Components;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class PublicInternshipOfferForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('year_id')
                    ->numeric(),
                Components\TextInput::make('organization_name')
                    ->maxLength(191),
                Components\TextInput::make('country')
                    ->maxLength(191),
                Components\TextInput::make('internship_type'),
                Components\TextInput::make('responsible_fullname')
                    ->maxLength(191),
                Components\TextInput::make('responsible_occupation')
                    ->maxLength(191),
                Components\TextInput::make('responsible_phone')
                    ->tel()
                    ->maxLength(191),
            ])
            ->model(IntenrshipOffer::class)
            ->statePath('data');
    }

    public function rules(): array
    {
        return [
            'record.year_id' => ['required', 'numeric'],
            'record.organization_name' => ['required', 'max:191'],
            'record.country' => ['required', 'max:191'],
            // ... validation rules for other form fields ...
        ];
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    /*     public function submit()
        {
            $this->validate();

            $this->store();

            $this->notify(__('Form submitted successfully!'));
        }
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.public-internship-offer-form'); //->layout('components.layouts.app');
    }
}
