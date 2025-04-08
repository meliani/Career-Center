<?php

namespace App\Filament\App\Widgets;

use App\Enums\CollaborationStatus;
use App\Enums\Role;
use App\Models\CollaborationRequest;
use App\Models\FinalYearInternshipAgreement;
use App\Models\MidTermReport;
use App\Models\Project;
use App\Models\ProjectAgreement;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use App\Notifications\AdminCollaborationNotification;
use App\Notifications\CollaborationRequestAccepted;
use App\Notifications\CollaborationRequestReceived;
use App\Notifications\CollaborationRequestRejected;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentProjectWidget extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.app.widgets.student-project-widget';

    protected int | string | array $columnSpan = '1';

    public $selectedStudent;

    public $message;

    public $selectedCollaborator;

    public bool $showCollaboratorForm = false;

    public $midTermReportContent;

    public function mount(): void
    {
        $this->collaboratorForm->fill();
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return [
            'collaboratorForm' => $this->makeForm()
                ->schema($this->getCollaboratorFormSchema()),
            'form' => $this->makeForm()
                ->schema($this->getMidTermFormSchema()),
        ];
    }

    protected function getMidTermFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('midTermReportContent')
                ->label('Mid-Term Report Content')
                ->required()
                ->maxLength(5000)
                ->rows(10),
        ];
    }

    protected function getCollaboratorFormSchema(): array
    {
        return [
            Forms\Components\Select::make('selectedCollaborator')
                ->label('Select Student')
                ->options(function () {
                    $currentStudent = auth()->user();

                    // dd($currentStudent->finalYearInternship);

                    return \App\Models\Student::query()
                        ->where('id', '!=', auth()->id())
                        ->where('level', $currentStudent->level->value)
                        ->whereHas('finalYearInternship', function ($agreementQuery) {
                            $agreementQuery->where('year_id', Year::current()->id)
                                ->whereHas('project', function ($projectQuery) {
                                    $projectQuery->whereHas('agreements', function ($agreementsQuery) {
                                        $agreementsQuery->where('agreeable_type', FinalYearInternshipAgreement::class);
                                    }, '<', 2);
                                });
                        })
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(function ($student) {
                            return [$student->id => "{$student->first_name} {$student->last_name} ({$student->id_pfe})"];
                        });
                })
                ->searchable()
                ->required()
                ->helperText('Only showing students from your level without projects'),
            Forms\Components\Textarea::make('message')
                ->label('Collaboration Message')
                ->placeholder('Example: I would like to collaborate with you on our final year project.')
                ->required()
                ->maxLength(255)
                ->helperText('Write a brief message explaining why you want to collaborate'),
        ];
    }

    protected function notifyAdministrators(CollaborationRequest $request, string $type = 'request'): void
    {
        $administrators = User::whereIN('role', Role::getAdministratorRoles())->get();
        foreach ($administrators as $admin) {
            $admin->notify(new AdminCollaborationNotification($request, $type));
        }
    }

    public function toggleCollaboratorForm(): void
    {
        $this->showCollaboratorForm = ! $this->showCollaboratorForm;
    }

    public function addCollaborator(): void
    {
        if (! $this->hasExistingProject()) {
            Notification::make()
                ->title('You must have an existing project before adding collaborators')
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'selectedCollaborator' => ['required', 'exists:students,id'],
        ]);

        $student = \App\Models\Student::find($this->selectedCollaborator);

        try {
            DB::transaction(function () use ($student) {
                // Clean up any old rejected/cancelled requests
                $this->cleanupOldRequests();

                // Create collaboration request
                $request = CollaborationRequest::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $student->id,
                    'message' => $this->message,
                    'status' => 'pending',
                    'year_id' => Year::current()->id,
                ]);

                // Notify the receiver
                $student->notify(new CollaborationRequestReceived($request));

                // Notify administrators with type 'request'
                $this->notifyAdministrators($request, 'request');
            });

            $this->reset(['selectedCollaborator', 'showCollaboratorForm']);

            Notification::make()
                ->title(
                    'A collaboration request has been sent to ' . $student->full_name .
                    ' ' . 'from ' . auth()->user()->full_name . '.'
                )
                ->success()
                ->send()
                ->sendToDatabase([
                    $student,
                    auth()->user(),
                ])
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getProject()
    {
        return Project::query()
            ->with(['timetable.room', 'timetable.timeslot', 'externalSupervisor', 'professors'])
            ->whereHas('agreements', function (Builder $query) {
                $query->whereHas('agreeable', function (Builder $query) {
                    $query->where('student_id', auth()->id())
                        ->where('year_id', Year::current()->id);
                });
            })
            ->first();
    }

    public function hasCollaborationRequest(): bool
    {
        // First clean up old rejected/cancelled requests
        $this->cleanupOldRequests();

        // Only check for pending or accepted requests
        return CollaborationRequest::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
            ->where('year_id', Year::current()->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
    }

    protected function cleanupOldRequests(): void
    {
        // Delete rejected/cancelled requests for the current student
        CollaborationRequest::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
            ->where('year_id', Year::current()->id)
            ->whereIn('status', ['rejected', 'cancelled'])
            ->delete();
    }

    public function getCollaborationRequest()
    {
        return CollaborationRequest::with(['sender', 'receiver'])
            ->where(function ($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->where('year_id', Year::current()->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();
    }

    public function acceptCollaborationRequest($requestId): void
    {
        $request = CollaborationRequest::findOrFail($requestId);

        if ($request->receiver_id !== auth()->id()) {
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Get both students' final year agreements
            $senderAgreement = FinalYearInternshipAgreement::where('student_id', $request->sender_id)
                ->where('year_id', Year::current()->id)
                ->first();

            $receiverAgreement = FinalYearInternshipAgreement::where('student_id', $request->receiver_id)
                ->where('year_id', Year::current()->id)
                ->first();

            if (! $senderAgreement || ! $receiverAgreement) {
                throw new \Exception('Required agreements not found');
            }

            // 2. Get both projects if they exist
            $senderProject = $senderAgreement->project;
            $receiverProject = $receiverAgreement->project;

            if (! $senderProject) {
                throw new \Exception('Sender project not found');
            }

            // 3. Move receiver's agreement to sender's project
            ProjectAgreement::updateOrCreate(
                [
                    'agreeable_id' => $receiverAgreement->id,
                    'agreeable_type' => FinalYearInternshipAgreement::class,
                ],
                ['project_id' => $senderProject->id]
            );

            // 4. Delete receiver's project if it exists (observer will handle cleanup)
            if ($receiverProject && $receiverProject->id !== $senderProject->id) {
                $receiverProject->delete();
            }

            // 5. Update collaboration request status
            $request->update(['status' => 'accepted']);

            DB::commit();

            // 6. Send notifications
            Notification::make()
                ->title('Collaboration request accepted')
                ->success()
                ->send();

            $request->sender->notify(new CollaborationRequestAccepted($request));
            // Notify administrators with type 'accepted'
            $this->notifyAdministrators($request, 'accepted');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Collaboration acceptance failed', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Error accepting collaboration request')
                ->body($e->getMessage())
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    public function rejectCollaborationRequest($requestId): void
    {
        $request = CollaborationRequest::findOrFail($requestId);

        if ($request->receiver_id !== auth()->id()) {
            return;
        }

        DB::transaction(function () use ($request) {
            $request->update(['status' => 'rejected']);

            // Notify the sender
            $request->sender->notify(new CollaborationRequestRejected($request));

            // Notify administrators with type 'rejected'
            $this->notifyAdministrators($request, 'rejected');

            Notification::make()
                ->title('Collaboration request rejected')
                ->warning()
                ->send();
        });
    }

    public function cancelCollaborationRequest($requestId): void
    {
        $request = CollaborationRequest::findOrFail($requestId);

        if ($request->sender_id !== auth()->id()) {
            return;
        }

        // check if the request is not accepted
        if ($request->status === CollaborationStatus::Accepted) {
            Notification::make()
                ->title('You cannot cancel an accepted collaboration request')
                ->danger()
                ->send();

            return;
        }

        $request->update(['status' => 'cancelled']);

        Notification::make()
            ->title('Collaboration request cancelled')
            ->warning()
            ->send();
    }

    // Add this helper method to check if user has existing project
    protected function hasExistingProject(): bool
    {
        return \App\Models\ProjectAgreement::query()
            ->whereHas('agreeable', function ($query) {
                $query->where('student_id', auth()->id())
                    ->where('year_id', Year::current()->id);
            })
            ->exists();
    }

    protected function hasActiveCollaboration(): bool
    {
        return CollaborationRequest::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
            ->where('year_id', Year::current()->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function submitMidTermReport(): void
    {
        $this->form->validate();

        $project = $this->getProject();

        if (! $project) {
            Notification::make()
                ->title('You must have an assigned project to submit a mid-term report.')
                ->danger()
                ->send();

            return;
        }

        try {
            MidTermReport::create([
                'student_id' => auth()->id(),
                'project_id' => $project->id,
                'submitted_at' => now(),
                'is_read_by_supervisor' => false,
                'content' => $this->midTermReportContent,
            ]);

            $this->reset('midTermReportContent');

            Notification::make()
                ->title('Mid-term report submitted successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to submit mid-term report.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getMidTermReport()
    {
        $project = $this->getProject();

        if (! $project) {
            return null;
        }

        return MidTermReport::where('student_id', auth()->id())
            ->where('project_id', $project->id)
            ->first();
    }
}
