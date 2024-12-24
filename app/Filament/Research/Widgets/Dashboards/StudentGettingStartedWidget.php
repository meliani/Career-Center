<?php

namespace App\Filament\Research\Widgets\Dashboards;

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

    public array $buttons = [];

    public function mount()
    {
        $this->buttons = [
            [
                'label' => 'Update Profile',
                'icon' => 'heroicon-o-user',
                'color' => 'primary',
                'action' => 'redirectToProfile',
            ],
            [
                'label' => 'Announce Internship',
                'icon' => 'heroicon-o-document-text',
                'color' => 'gray',
                'action' => 'redirectToAnnounceInternship',
            ],
        ];

        $this->loadStudentProgress();
    }

    protected function loadStudentProgress()
    {
        $student = auth()->user();

        // Check for required profile fields
        $hasBasicProfile = $student->is_verified;
        $hasAvatar = ! empty($student->avatar_url);
        $hasContactInfo = ! empty($student->email_perso) && ! empty($student->phone);
        $hasDocuments = ! empty($student->cv) && ! empty($student->lm);

        // Get offers view statistics
        $viewedOffersCount = $student->getViewedOffersCount();
        $minimumOffersToView = 3; // You can adjust this number
        $hasViewedEnoughOffers = $viewedOffersCount >= $minimumOffersToView;

        $hasCompleteProfile = $hasBasicProfile && $hasAvatar && $hasContactInfo && $hasDocuments;

        $hasApplications = InternshipApplication::where('student_id', $student->id)->exists();
        $hasAgreement = FinalYearInternshipAgreement::where('student_id', $student->id)->exists();
        $this->hasAgreement = $hasAgreement;

        $this->steps = [
            [
                'title' => __('Complete Profile'),
                'status' => $hasCompleteProfile ? 'completed' : 'current',
                'details' => [
                    'avatar' => ['status' => $hasAvatar, 'label' => __('Profile Picture')],
                    'contact' => ['status' => $hasContactInfo, 'label' => __('Contact Information')],
                    'documents' => ['status' => $hasDocuments, 'label' => __('CV & Cover Letter')],
                ],
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

        // Calculate progress including sub-items and viewed offers
        $totalSteps = count($this->steps) + 5; // +5 for profile sub-items and viewed offers requirements
        $completed = collect($this->steps)->where('status', 'completed')->count();
        if ($hasAvatar) {
            $completed++;
        }
        if ($hasContactInfo) {
            $completed++;
        }
        if ($hasDocuments) {
            $completed++;
        }

        // Add partial progress for viewed offers
        if ($viewedOffersCount > 0) {
            if ($hasViewedEnoughOffers) {
                $completed += 2; // Full credit for viewing enough offers
            } else {
                $completed += ($viewedOffersCount / $minimumOffersToView); // Partial credit based on progress
            }
        }

        $this->progress = ($completed / $totalSteps) * 100;
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
            return redirect()->route('filament.research.pages.my-profile'); // Update the route name if different
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
            return redirect()->route('filament.research.resources.internship-offers.index'); // Update the route name if different
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
                return redirect()->route('filament.research.resources.master-research-internship-agreements.index'); // Update the route name if different
            }

            return redirect()->route('filament.research.resources.master-research-internship-agreements.create'); // Update the route name if different
        }

        // Optionally, redirect to the login page if not authenticated
        return redirect()->route('login');
    }
}
