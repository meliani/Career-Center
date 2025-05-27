<?php

namespace App\Filament\App\Widgets;

use App\Models\RescheduleRequest;
use App\Models\Timetable;
use App\Models\Student;
use Filament\Widgets\Widget;

class StudentDefenseWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.student-defense-widget';

    protected static ?int $sort = 3;

    public $upcomingDefense = null;
    public $rescheduleRequest = null;
    public $canRequestReschedule = false;

    public function mount()
    {
        $this->loadStudentDefenseInfo();
    }    protected function loadStudentDefenseInfo()
    {
        $studentId = auth()->id();
          // Get the student's upcoming defense if any
        // Students are connected to timetables through projects via internship agreements
        $this->upcomingDefense = Timetable::whereHas('project', function ($query) use ($studentId) {
                $query->whereHas('final_internship_agreements', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                });
            })
            ->with(['timeslot', 'room', 'project'])
            ->orderBy('created_at', 'desc')
            ->first();
            
        // Check if there's an active reschedule request
        $this->rescheduleRequest = RescheduleRequest::where('student_id', $studentId)
            ->where('timetable_id', $this->upcomingDefense?->id)
            ->latest()
            ->first();
            
        // Determine if the student can request a reschedule
        // Students can request a reschedule if they have a defense scheduled
        // and don't already have a pending or approved request
        $this->canRequestReschedule = 
            $this->upcomingDefense !== null && 
            ($this->rescheduleRequest === null || 
             $this->rescheduleRequest->status->value === 'rejected');
    }

    /**
     * Redirect the user to the Create Reschedule Request page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToRescheduleForm()
    {
        // Ensure the user is authenticated and has a defense
        if (auth()->check() && $this->upcomingDefense) {
            // Using URL helper since we'll create this as a separate page
            return redirect()->to(route('filament.app.pages.request-defense-reschedule'));
        }

        return redirect()->route('filament.app.pages.welcome-dashboard');
    }

    /**
     * Redirect the user to view the reschedule request details.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function viewRescheduleRequest()
    {
        if (auth()->check() && $this->rescheduleRequest) {
            return redirect()->to(route('filament.app.pages.request-defense-reschedule', ['rescheduleRequest' => $this->rescheduleRequest->id]));
        }

        return redirect()->route('filament.app.pages.welcome-dashboard');
    }
}
