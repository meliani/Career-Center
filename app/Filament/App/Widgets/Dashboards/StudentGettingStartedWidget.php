<?php

namespace App\Filament\App\Widgets\Dashboards;

use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipApplication;
use Filament\Widgets\Widget;

class StudentGettingStartedWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.student-getting-started-widget';

    protected static ?int $sort = 1;

    public array $steps = [];

    public int $progress = 0;

    public $hasAgreement = false;

    public function mount()
    {
        $this->loadStudentProgress();
    }

    protected function loadStudentProgress()
    {
        $student = auth()->user();
        $hasProfile = $student->is_verified;
        $hasApplications = InternshipApplication::where('student_id', $student->id)->exists();
        $hasAgreement = FinalYearInternshipAgreement::where('student_id', $student->id)->exists();
        $this->hasAgreement = $hasAgreement;

        $this->steps = [
            [
                'title' => __('Complete Profile'),
                'status' => $hasProfile ? 'completed' : 'current',
            ],
            [
                'title' => __('Check Internship Offers'),
                'status' => $hasProfile ? 'current' : 'pending',
            ],
            [
                'title' => __('Apply to Offers'),
                'status' => $hasApplications ? 'completed' : ($hasProfile ? 'current' : 'pending'),
            ],
            [
                'title' => __('Announce Internship'),
                'status' => $hasAgreement ? 'completed' : ($hasApplications ? 'current' : 'pending'),
            ],
            [
                'title' => __('Get your Internship Agreement'),
                'status' => $hasAgreement ? 'completed' : 'pending',
            ],
        ];

        // Calculate progress
        $completed = collect($this->steps)->where('status', 'completed')->count();
        $this->progress = ($completed / count($this->steps)) * 100;
    }

    /**
     * Redirect the user to the Edit Profile page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProfile()
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            return redirect()->route('filament.app.pages.my-profile'); // Update the route name if different
        }

        // Optionally, redirect to the login page if not authenticated
        return redirect()->route('login');
    }

    /**
     * Redirect the user to the Internship Offers page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToOffers()
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            return redirect()->route('filament.app.resources.internship-offers.index'); // Update the route name if different
        }

        // Optionally, redirect to the login page if not authenticated
        return redirect()->route('login');
    }

    /**
     * Redirect the user to the Announce Internship
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToAnnounceInternship()
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            // if has agreement redirect to the agreement page
            if ($this->hasAgreement) {
                return redirect()->route('filament.app.resources.final-year-internship-agreements.index'); // Update the route name if different
            }

            return redirect()->route('filament.app.resources.final-year-internship-agreements.create'); // Update the route name if different
        }

        // Optionally, redirect to the login page if not authenticated
        return redirect()->route('login');
    }
}
