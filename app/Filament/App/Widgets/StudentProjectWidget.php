<?php

namespace App\Filament\App\Widgets;

use App\Models\CollaborationRequest;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Project;
use App\Models\ProjectAgreement;
use App\Models\Student;
use App\Models\Year;
use App\Notifications\CollaborationRequestAccepted;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected $listeners = ['closeModal'];

    public function mount(): void
    {
        $this->form->fill();
        $this->collaboratorForm->fill();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema()),
            'collaboratorForm' => $this->makeForm()
                ->schema($this->getCollaboratorFormSchema()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('selectedStudent')
                ->label('Select Student')
                ->options(function () {
                    $currentStudent = auth()->user();

                    return \App\Models\Student::query()
                        ->where('id', '!=', auth()->id())
                        ->where('level', $currentStudent->level)
                        ->whereDoesntHave('projects', function ($query) {
                            $query->where('year_id', Year::current()->id);
                        })
                        ->whereDoesntHave('collaborationRequests', function ($query) {
                            $query->where('year_id', Year::current()->id)
                                ->whereIn('status', ['pending', 'accepted']);
                        })
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn ($student) => [$student->id => "{$student->name} ({$student->id_pfe})"]);
                })
                ->searchable()
                ->placeholder('Choose a student to collaborate with')
                ->helperText('Only showing students from your level without projects')
                ->required(),
            Forms\Components\Textarea::make('message')
                ->label('Collaboration Message')
                ->placeholder('Example: I would like to collaborate with you on our final year project.')
                ->required()
                ->maxLength(255)
                ->helperText('Write a brief message explaining why you want to collaborate'),
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
                                ->whereHas('project');
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
        ];
    }

    public function sendCollaborationRequest(): void
    {
        if (! $this->hasExistingProject()) {
            Notification::make()
                ->title('You must have an existing project or internship agreement before sending collaboration requests')
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'selectedStudent' => 'required|exists:students,id',
            'message' => 'required|string|max:255',
        ]);

        // Clean up any old rejected/cancelled requests
        $this->cleanupOldRequests();

        // Create new collaboration request
        CollaborationRequest::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedStudent,
            'message' => $this->message,
            'status' => 'pending',
            'year_id' => Year::current()->id,
        ]);

        // Reset form
        $this->reset(['selectedStudent', 'message']);

        Notification::make()
            ->title('Collaboration request sent successfully')
            ->success()
            ->send();
    }

    public function openCollaborationModal(): void
    {
        $this->dispatch('open-modal', [
            'id' => 'collaboration-modal',
        ]);
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
            // Clean up any old rejected/cancelled requests
            $this->cleanupOldRequests();

            // Create collaboration request
            CollaborationRequest::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $student->id,
                'message' => 'Would you like to collaborate on my project?',
                'status' => 'pending',
                'year_id' => Year::current()->id,
            ]);

            $this->reset(['selectedCollaborator', 'showCollaboratorForm']);

            Notification::make()
                ->title('Collaboration request sent successfully')
                ->success()
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
            ->with(['timetable.room', 'timetable.timeslot', 'externalSupervisor'])
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

        $request->update(['status' => 'rejected']);

        Notification::make()
            ->title('Collaboration request rejected')
            ->warning()
            ->send();
    }

    public function cancelCollaborationRequest($requestId): void
    {
        $request = CollaborationRequest::findOrFail($requestId);

        if ($request->sender_id !== auth()->id()) {
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
}
