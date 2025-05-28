<?php

namespace App\Filament\App\Pages;

use App\Models\RescheduleRequest;
use App\Models\Timetable;
use App\Models\Timeslot;
use App\Models\Student;
use App\Models\Room;
use App\Enums\RescheduleRequestStatus;
use App\Notifications\RescheduleRequestSubmitted;
use App\Services\RoomService;
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

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    public function getTitle(): string
    {
        return __('Request Defense Reschedule');
    }

    public static function getNavigationLabel(): string
    {
        return __('Defense Reschedule');
    }

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
                'preferred_room_id' => $this->existingRequest->preferred_room_id,
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
                Section::make(__('Current Defense Schedule'))
                    ->schema([
                        ViewField::make('current_schedule')
                            ->view('filament.app.forms.components.current-defense-schedule')
                            ->viewData([
                                'timetable' => $this->timetable,
                            ]),
                    ])
                    ->columns(1),
                  Section::make(__('Reschedule Request'))
                    ->schema([
                        Filament\Forms\Components\Hidden::make('timetable_id'),
                        
                        Textarea::make('reason')
                            ->label(__('Reason for Reschedule'))
                            ->placeholder(__('Please explain why you need to reschedule your defense'))
                            ->required()
                            ->disabled(function () {
                                return $this->existingRequest && 
                                    $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                            })
                            ->minLength(20)
                            ->maxLength(500)
                            ->columnSpanFull(),                        Filament\Forms\Components\Select::make('preferred_timeslot_id')
                            ->label(__('Preferred Timeslot'))
                            ->options(function () {
                                // Get ALL future timeslots - no filtering by availability
                                $timeslots = Timeslot::where('start_time', '>=', now()->addHours(24))
                                    ->orderBy('start_time')
                                    ->get();
                                
                                $availableTimeslots = [];
                                
                                foreach ($timeslots as $timeslot) {
                                    // Show ALL timeslots with localized formatting
                                    $label = $timeslot->start_time->locale(app()->getLocale())->isoFormat('dddd, LL - HH:mm') . 
                                             ' ' . __('to') . ' ' . 
                                             $timeslot->end_time->format('H:i');
                                    
                                    $availableTimeslots[$timeslot->id] = $label;
                                }
                                
                                return $availableTimeslots;
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                // Clear room selection when timeslot changes
                                $set('preferred_room_id', null);
                            })
                            ->disabled(function () {
                                return $this->existingRequest && 
                                    $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                            })
                            ->helperText(function () {
                                $totalTimeslots = Timeslot::where('start_time', '>=', now()->addHours(24))->count();
                                return __('Choose from :count available future timeslots. Room availability will be checked when you select a timeslot.', ['count' => $totalTimeslots]);
                            })
                            ->columnSpanFull(),

                        Filament\Forms\Components\Select::make('preferred_room_id')
                            ->label(__('Preferred Room'))
                            ->options(function (callable $get) {
                                $timeslotId = $get('preferred_timeslot_id');
                                
                                if (!$timeslotId) {
                                    return [];
                                }
                                
                                $timeslot = Timeslot::find($timeslotId);
                                if (!$timeslot) {
                                    return [];
                                }
                                
                                // Get ALL active rooms, regardless of availability
                                return Room::where('status', \App\Enums\RoomStatus::Available)
                                    ->get()
                                    ->mapWithKeys(function ($room) use ($timeslot) {
                                        // Enhanced room display with capacity and availability information
                                        $label = $room->name;
                                        if ($room->capacity) {
                                            $label .= ' (' . __('Capacity') . ': ' . $room->capacity . ')';
                                        }
                                        if ($room->building) {
                                            $label .= ' - ' . $room->building;
                                        }
                                        
                                        // Check availability and add indicator
                                        $isAvailable = RoomService::checkRoomAvailability($timeslot, $room, $this->timetable?->id);
                                        if (!$isAvailable) {
                                            $label .= ' ⚠️ ' . __('(Unavailable)');
                                        }
                                        
                                        return [$room->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(function (callable $get) {
                                $timeslotSelected = $get('preferred_timeslot_id');
                                $isExistingRequest = $this->existingRequest && 
                                    $this->existingRequest->status !== RescheduleRequestStatus::Rejected;
                                
                                return !$timeslotSelected || $isExistingRequest;
                            })
                            ->helperText(function (callable $get) {
                                $timeslotId = $get('preferred_timeslot_id');
                                
                                if (!$timeslotId) {
                                    return __('Please select a timeslot first.');
                                }
                                
                                $timeslot = Timeslot::find($timeslotId);
                                if (!$timeslot) {
                                    return __('Invalid timeslot selected.');
                                }
                                
                                // Get ALL rooms and analyze availability
                                $allRooms = Room::where('status', \App\Enums\RoomStatus::Available)->get();
                                $availableRooms = $allRooms->filter(function ($room) use ($timeslot) {
                                    return RoomService::checkRoomAvailability($timeslot, $room, $this->timetable?->id);
                                });
                                
                                $totalCount = $allRooms->count();
                                $availableCount = $availableRooms->count();
                                $unavailableCount = $totalCount - $availableCount;
                                
                                // Check professor availability for this timeslot
                                $project = $this->timetable->project;
                                $professorsAvailable = \App\Services\ProfessorService::checkJuryAvailability(
                                    $timeslot, 
                                    $project, 
                                    $this->timetable->id
                                );
                                
                                $warnings = [];
                                if (!$professorsAvailable) {
                                    $warnings[] = __('⚠️ Some professors may not be available for this timeslot');
                                }
                                
                                // Show room availability statistics
                                $details = [__(':total rooms total', ['total' => $totalCount])];
                                
                                if ($availableCount > 0) {
                                    $details[] = __(':count available', ['count' => $availableCount]);
                                }
                                
                                if ($unavailableCount > 0) {
                                    $details[] = __(':count unavailable', ['count' => $unavailableCount]);
                                }
                                
                                // Show some details about all rooms
                                $capacityRange = $allRooms->pluck('capacity')->filter()->unique()->sort()->values();
                                $buildings = $allRooms->pluck('building')->filter()->unique()->values();
                                
                                if ($capacityRange->count() > 0) {
                                    $min = $capacityRange->first();
                                    $max = $capacityRange->last();
                                    if ($min === $max) {
                                        $details[] = __('Room capacity') . ': ' . $min;
                                    } else {
                                        $details[] = __('Room capacities') . ': ' . $min . '-' . $max;
                                    }
                                }
                                
                                if ($buildings->count() > 0) {
                                    $details[] = __('Buildings') . ': ' . $buildings->take(3)->join(', ');
                                }
                                
                                $info = implode(' | ', $details);
                                
                                if ($unavailableCount > 0) {
                                    $info .= ' | ⚠️ ' . __('Unavailable rooms are marked with warning icon');
                                }
                                
                                return $warnings ? implode(' ', $warnings) . ' | ' . $info : $info;
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(function () {
                        return !$this->existingRequest || 
                            $this->existingRequest->status === RescheduleRequestStatus::Rejected;
                    }),
                
                Section::make(__('Request Status'))
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
                ->title(__('Request Already Exists'))
                ->body(__('You already have an active reschedule request. Please wait for it to be processed.'))
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
                throw new \Exception(__('Selected timeslot no longer exists.'));
            }
            
            // Verify that the selected room is still available
            $room = Room::find($data['preferred_room_id']);
            if (!$room) {
                throw new \Exception(__('Selected room no longer exists.'));
            }
            
            // Check if the timeslot+room combination is still available using RoomService
            $isRoomAvailable = RoomService::checkRoomAvailability($timeslot, $room, $this->timetable?->id);
            
            if (!$isRoomAvailable) {
                throw new \Exception(__('The selected room is no longer available for this timeslot.'));
            }
            
            // Verify that professors are still available
            $project = $this->timetable->project;
            $professorsAvailable = \App\Services\ProfessorService::checkJuryAvailability(
                $timeslot, 
                $project, 
                $this->timetable->id
            );
            
            if (!$professorsAvailable) {
                throw new \Exception(__('One or more professors are no longer available at this timeslot.'));
            }
            
            // Create or update the reschedule request
            if ($this->existingRequest && 
                $this->existingRequest->status === RescheduleRequestStatus::Rejected) {
                  $this->existingRequest->update([
                    'reason' => $data['reason'],
                    'preferred_timeslot_id' => $data['preferred_timeslot_id'],
                    'preferred_room_id' => $data['preferred_room_id'],
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
                    'preferred_room_id' => $data['preferred_room_id'],
                    'status' => RescheduleRequestStatus::Pending,
                ]);
            }
            
            DB::commit();
            
            // Notify administrators about the new request
            RescheduleRequestSubmitted::sendToAdmins($request);
              // Show success notification
            Notification::make()
                ->title(__('Reschedule request submitted successfully'))
                ->success()
                ->send();
                
            // Update the current page state instead of redirecting
            $this->existingRequest = $request;
            
            // Fill form with the new request data
            $this->form->fill([
                'timetable_id' => $request->timetable_id,
                'reason' => $request->reason,
                'preferred_timeslot_id' => $request->preferred_timeslot_id,
                'preferred_room_id' => $request->preferred_room_id,
            ]);
            
            return;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the specific error
            \Illuminate\Support\Facades\Log::error('Error submitting reschedule request: ' . $e->getMessage());
            
            // Show error notification
            Notification::make()
                ->title(__('Error submitting reschedule request'))
                ->body($e->getMessage() ?: __('Please try again later or contact support if the problem persists.'))
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
                'preferred_room_id' => $this->existingRequest->preferred_room_id,
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
