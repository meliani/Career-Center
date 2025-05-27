<?php

namespace App\Livewire;

use App\Models\RescheduleRequest;
use App\Models\Timetable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class RescheduleRequestForm extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];
    public $timetable;
    public $showForm = false;
    
    public function mount($timetableId)
    {
        $this->timetable = Timetable::findOrFail($timetableId);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('preferred_date')
                    ->label(__('Preferred Date'))
                    ->required()
                    ->minDate(now()->addDays(1)),
                    
                TimePicker::make('preferred_time')
                    ->label(__('Preferred Time'))
                    ->seconds(false)
                    ->required(),
                    
                Textarea::make('reason')
                    ->label(__('Reason for Rescheduling'))
                    ->placeholder(__('Please explain why you need to reschedule your defense'))
                    ->required()
                    ->minLength(10)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }
    
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }
    
    public function submit()
    {
        $data = $this->form->getState();
        
        // Check for existing pending requests
        $existingRequest = RescheduleRequest::where('timetable_id', $this->timetable->id)
            ->where('student_id', auth()->user()->student->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            Notification::make()
                ->title(__('You already have a pending reschedule request'))
                ->body(__('Please wait for the staff to process your existing request'))
                ->warning()
                ->send();
                
            return;
        }
        
        // Create the reschedule request
        RescheduleRequest::create([
            'timetable_id' => $this->timetable->id,
            'student_id' => auth()->user()->student->id,
            'status' => 'pending',
            'reason' => $data['reason'],
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'],
        ]);
        
        $this->showForm = false;
        $this->reset('data');
        
        Notification::make()
            ->title(__('Reschedule Request Submitted'))
            ->body(__('Your request has been submitted and is pending review'))
            ->success()
            ->send();
            
        $this->dispatch('reschedule-request-submitted');
    }
    
    public function render()
    {
        return view('livewire.reschedule-request-form');
    }
}
