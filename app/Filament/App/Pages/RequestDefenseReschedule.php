<?php

namespace App\Filament\App\Pages;

use App\Models\RescheduleRequest;
use App\Models\Timetable;
use App\Models\Timeslot;
use App\Models\Student;
use App\Enums\RescheduleRequestStatus;
use App\Notifications\RescheduleRequestSubmitted;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament;

class RequestDefenseReschedule extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.app.pages.request-defense-reschedule';

    protected static ?string $title = 'Request Defense Reschedule';

    protected static ?string $navigationLabel = 'Defense Reschedule';

    protected static ?int $navigationSort = 5;

    // Hide from navigation menu - users will access via button on dashboard
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    
    public $timetable = null;
    public $existingRequest = null;

    public function mount($rescheduleRequest = null): void
    {        
        $studentId = auth()->id();
        
        // Get the student's timetable through the project's final_internship_agreements
        $this->timetable = Timetable::whereHas('project', function ($query) use ($studentId) {
                $query->whereHas('final_internship_agreements', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                });
            })
            ->with(['timeslot', 'room', 'project'])
            ->orderBy('created_at', 'desc')
            ->first();        
            
        // If we don't have a timetable yet, redirect to dashboard
        if (!$this->timetable) {
            $this->redirect(route('filament.app.pages.welcome-dashboard'));
            return;
        }
        
        // First, check if we have any existing request for this timetable
        $existingRequest = RescheduleRequest::where('student_id', $studentId)
            ->where('timetable_id', $this->timetable->id)
            ->latest()
            ->first();
          
        // Check if we're viewing a specific existing request
        if ($rescheduleRequest) {
            $specificRequest = RescheduleRequest::where('id', $rescheduleRequest)
                ->where('student_id', $studentId)
                ->first();
                
            if (!$specificRequest) {
                $this->redirect(route('filament.app.pages.welcome-dashboard'));
                return;
            }
            
            $this->existingRequest = $specificRequest;
        } else {
            // Use the most recent request if it exists
            $this->existingRequest = $existingRequest;
        }
        
        // Fill form based on whether we have an existing request
        if ($this->existingRequest) {
            $this->form->fill([
                'timetable_id' => $this->existingRequest->timetable_id,
                'reason' => $this->existingRequest->reason,
                'preferred_timeslot_id' => $this->existingRequest->preferred_timeslot_id,
            ]);
        } else {
            // Fill with default data
            $this->form->fill([
                'timetable_id' => $this->timetable->id,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Current Defense Schedule')
                    ->schema([
                        ViewField::make('current_schedule')
                            ->view('filament.app.forms.components.current-defense-schedule')
                            ->viewData([
                                'timetable' => $this->timetable,
                            ]),
                    ])
                    ->columns(1),
                  Section::make('Reschedule Request')
                    ->schema([
                        Filament\Forms\Components\Hidden::make('timetable_id'),
                        
                        Textarea::make('reason')
                            ->label('Reason for Reschedule')
                            ->placeholder('Please explain why you need to reschedule your defense')
                            ->required()
                            ->disabled(function () {
                                return $this->existingRequest && 
                                    $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                            })
                            ->minLength(20)
                            ->maxLength(500)
                            ->columnSpanFull(),                        Filament\Forms\Components\Select::make('preferred_timeslot_id')
                            ->label('Preferred Timeslot')                            ->options(function () {
                                // Get only future timeslots
                                $timeslots = Timeslot::where('start_time', '>=', now()->addDays(3))
                                    ->where('start_time', '<=', now()->addDays(30))
                                    ->orderBy('start_time')
                                    ->get();
                                
                                $availableTimeslots = [];
                                
                                // Get the current project from the timetable to check professor availability
                                $project = $this->timetable->project;
                                
                                foreach ($timeslots as $timeslot) {
                                    // Check if this timeslot has available rooms
                                    $usedRoomIds = Timetable::where('timeslot_id', $timeslot->id)
                                        ->pluck('room_id')
                                        ->toArray();
                                    
                                    $availableRooms = \App\Models\Room::where('status', \App\Enums\RoomStatus::Available)
                                        ->whereNotIn('id', $usedRoomIds)
                                        ->count();
                                    
                                    // Check if professors are available for this timeslot
                                    $professorsAvailable = \App\Services\ProfessorService::checkJuryAvailability(
                                        $timeslot, 
                                        $project, 
                                        $this->timetable->id
                                    );
                                      // Only add timeslots that have available rooms AND all professors are available
                                    if (($availableRooms > 0 || $usedRoomIds === []) && $professorsAvailable) {
                                        $label = $timeslot->start_time->format('l, F j, Y - H:i') . ' to ' . 
                                            $timeslot->end_time->format('H:i');
                                        
                                        $availableTimeslots[$timeslot->id] = $label;
                                    }
                                }
                                
                                return $availableTimeslots;
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(function () {
                                return $this->existingRequest && 
                                    $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(function () {
                        return !$this->existingRequest || 
                            $this->existingRequest->status === RescheduleRequestStatus::Rejected;
                    }),
                
                Section::make('Request Status')
                    ->schema([
                        ViewField::make('request_status')
                            ->view('filament.app.forms.components.reschedule-request-status')
                            ->viewData([
                                'request' => $this->existingRequest,
                            ]),
                    ])
                    ->columns(1)
                    ->visible(function () {
                        return $this->existingRequest && 
                            $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                    }),
            ])
            ->statePath('data');
    }    public function submit(): void
    {        
        // Check if we have a non-rejected existing request
        if ($this->existingRequest && 
            $this->existingRequest->status !== RescheduleRequestStatus::Rejected) {
            // Show notification instead of redirecting
            Notification::make()
                ->title('Request Already Exists')
                ->body('You already have an active reschedule request. Please wait for it to be processed.')
                ->warning()
                ->send();
            return;
        }

        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();
            
            // Verify that the selected timeslot is still valid
            $timeslot = Timeslot::find($data['preferred_timeslot_id']);
            if (!$timeslot) {
                throw new \Exception('Selected timeslot no longer exists.');
            }
            
            // Verify that professors are still available
            $project = $this->timetable->project;
            $professorsAvailable = \App\Services\ProfessorService::checkJuryAvailability(
                $timeslot, 
                $project, 
                $this->timetable->id
            );
            
            if (!$professorsAvailable) {
                throw new \Exception('One or more professors are no longer available at this timeslot.');
            }
            
            // Create or update the reschedule request
            if ($this->existingRequest && 
                $this->existingRequest->status === RescheduleRequestStatus::Rejected) {
                  $this->existingRequest->update([
                    'reason' => $data['reason'],
                    'preferred_timeslot_id' => $data['preferred_timeslot_id'],
                    'status' => RescheduleRequestStatus::Pending,
                    'admin_notes' => null,
                    'processed_by' => null,
                    'processed_at' => null,
                ]);
                
                $request = $this->existingRequest;
            } else {
                $request = RescheduleRequest::create([
                    'timetable_id' => $data['timetable_id'],
                    'student_id' => auth()->id(),
                    'reason' => $data['reason'],
                    'preferred_timeslot_id' => $data['preferred_timeslot_id'],
                    'status' => RescheduleRequestStatus::Pending,
                ]);
            }
            
            DB::commit();
            
            // Notify administrators about the new request
            RescheduleRequestSubmitted::sendToAdmins($request);
              // Show success notification
            Notification::make()
                ->title('Reschedule request submitted successfully')
                ->success()
                ->send();
                
            // Update the current page state instead of redirecting
            $this->existingRequest = $request;
            
            // Fill form with the new request data
            $this->form->fill([
                'timetable_id' => $request->timetable_id,
                'reason' => $request->reason,
                'preferred_timeslot_id' => $request->preferred_timeslot_id,
            ]);
            
            return;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the specific error
            \Illuminate\Support\Facades\Log::error('Error submitting reschedule request: ' . $e->getMessage());
            
            // Show error notification
            Notification::make()
                ->title('Error submitting reschedule request')
                ->body($e->getMessage() ?: 'Please try again later or contact support if the problem persists.')
                ->danger()
                ->send();
        }
    }public function refreshRequestStatus(): void
    {
        // Refresh the existing request data
        if ($this->existingRequest) {
            $this->existingRequest = $this->existingRequest->fresh();
        } else {
            // Check for any new requests
            $studentId = auth()->id();
            $this->existingRequest = RescheduleRequest::where('student_id', $studentId)
                ->where('timetable_id', $this->timetable->id)
                ->latest()
                ->first();
        }
        
        // Refresh the form data
        if ($this->existingRequest) {
            $this->form->fill([
                'timetable_id' => $this->existingRequest->timetable_id,
                'reason' => $this->existingRequest->reason,
                'preferred_timeslot_id' => $this->existingRequest->preferred_timeslot_id,
            ]);
        }
    }

    protected function getFormActions(): array
    {
        // We're handling the actions in the view directly with wire:click
        return [];
    }

    public static function canAccess(): bool
    {
        // Check if the user is a student
        // check if the user is authenticated is instance of Student
        $user = auth()->user();
        return auth()->check() && $user instanceof Student;
    }
}
