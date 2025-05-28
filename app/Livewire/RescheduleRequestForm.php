<?php

namespace App\Livewire;

use App\Models\RescheduleRequest;
use App\Models\Room;
use App\Models\Timetable;
use App\Models\Timeslot;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                Select::make('preferred_timeslot_id')
                    ->label(__('Preferred Timeslot'))
                    ->placeholder(__('Select a preferred timeslot'))
                    ->options($this->getAvailableTimeslots())
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->resetField('preferred_room_id')),
                    
                Select::make('preferred_room_id')
                    ->label(__('Preferred Room'))
                    ->placeholder(__('Select a room'))
                    ->options(fn (callable $get) => $this->getAvailableRooms($get('preferred_timeslot_id')))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (callable $get) => !$get('preferred_timeslot_id'))
                    ->helperText(__('Select a timeslot first to see available rooms')),
                    
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
            ->where('student_id', auth()->id())
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
        
        // Check if the selected timeslot+room combination is still available
        $isAlreadyTaken = Timetable::where('timeslot_id', $data['preferred_timeslot_id'])
            ->where('room_id', $data['preferred_room_id'])
            ->exists();
            
        if ($isAlreadyTaken) {
            Notification::make()
                ->title(__('Timeslot and Room combination is no longer available'))
                ->body(__('Please select a different timeslot or room'))
                ->warning()
                ->send();
                
            return;
        }
        
        // Create the reschedule request
        RescheduleRequest::create([
            'timetable_id' => $this->timetable->id,
            'student_id' => auth()->id(),
            'status' => 'pending',
            'reason' => $data['reason'],
            'preferred_timeslot_id' => $data['preferred_timeslot_id'],
            'preferred_room_id' => $data['preferred_room_id'],
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
    
    private function getAvailableTimeslots()
    {
        // Get future timeslots, excluding the current one
        return Timeslot::where('start_time', '>', now())
            ->where('id', '!=', $this->timetable->timeslot_id)
            ->orderBy('start_time')
            ->get()
            ->mapWithKeys(function ($timeslot) {
                return [
                    $timeslot->id => $timeslot->start_time->format('M d, Y - H:i')
                ];
            });
    }

    private function getAvailableRooms($timeslotId)
    {
        if (!$timeslotId) {
            return [];
        }

        // Get all available rooms
        $allRooms = Room::available()->get();
        
        // Get rooms that are already taken for this timeslot
        $takenRooms = Timetable::where('timeslot_id', $timeslotId)
            ->pluck('room_id')
            ->toArray();

        // Return rooms that are not taken for this timeslot
        return $allRooms->whereNotIn('id', $takenRooms)
            ->mapWithKeys(function ($room) {
                return [
                    $room->id => $room->name . ($room->description ? ' - ' . $room->description : '')
                ];
            });
    }

    public function resetField($field)
    {
        $this->data[$field] = null;
    }
    
    public function render()
    {
        return view('livewire.reschedule-request-form');
    }
}
