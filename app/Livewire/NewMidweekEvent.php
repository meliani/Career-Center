<?php

namespace App\Livewire;

use App\Models\MidweekEvent;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class NewMidweekEvent extends Page implements HasForms
{
    use InteractsWithForms;

    protected ?string $heading = 'New Midweek Event';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->model(MidweekEvent::class)
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->content(__('Midweek Pro Connect is a weekly event that takes place every Wednesday. It is a great opportunity for students and companies to connect and discuss potential internships, job opportunities, and more.')),
                Forms\Components\Placeholder::make('Give your event a name'),
                Forms\Components\TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\Select::make('organization_account_id')
                    ->relationship('organizationAccount', 'name')
                    ->required(),
                // Forms\Components\Select::make('meeting_confirmed_by')
                //     ->relationship('meetingConfirmedBy', 'name')
                //     ->required(),
                // Forms\Components\DateTimePicker::make('meeting_confirmed_at'),
                // Forms\Components\Select::make('room_id')
                //     ->relationship('room', 'name')
                //     ->required(),
                Forms\Components\Select::make('midweek_event_session_id')
                    ->label('Session')
                    ->relationship('midweekEventSession', 'session_start_at')
                    ->required(),
                Forms\Components\Placeholder::make('')
                    ->content(__('In the bottom of the form, you can see our sessions calendar.')),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);

    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = MidweekEvent::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title(__('Your new midweek event has been submitted!'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.new-midweek-event'); //->layout('components.layouts.public');
    }
}
