<?php

namespace App\Filament\App\Widgets\Dashboards;

use App\Models\Apprenticeship;
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
                'label' => 'View Offers',
                'icon' => 'heroicon-o-briefcase',
                'color' => 'success',
                'action' => 'redirectToOffers',
            ],
            [
                'label' => 'Create Agreement',
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
        
        // Check for agreements based on student level
        if ($student->level === \App\Enums\StudentLevel::FirstYear || 
            $student->level === \App\Enums\StudentLevel::SecondYear) {
            // For first and second year students, check for apprenticeship agreements
            $hasAgreement = \App\Models\Apprenticeship::where('student_id', $student->id)->exists();
        } else {
            // For third year students, check for final year internship agreements
            $hasAgreement = FinalYearInternshipAgreement::where('student_id', $student->id)->exists();
        }
        
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
                'title' => __('Check Internship Offers'),
                'status' => $hasViewedEnoughOffers ? 'completed' : ($hasCompleteProfile ? 'current' : 'pending'),
                'details' => [
                    'viewed' => [
                        'status' => $viewedOffersCount > 0,
                        'label' => __(':count/:required Offers Viewed', [
                            'count' => $viewedOffersCount,
                            'required' => $minimumOffersToView,
                        ]),
                    ],
                    'progress' => [
                        'status' => $hasViewedEnoughOffers,
                        'label' => $hasViewedEnoughOffers
                            ? __('Enough offers viewed')
                            : __('View :more more offers', ['more' => $minimumOffersToView - $viewedOffersCount]),
                    ],
                ],
            ],
            [
                'title' => __('Apply to Offers'),
                'status' => $hasApplications ? 'completed' : ($hasViewedEnoughOffers ? 'current' : 'pending'),
            ],
            [
                'title' => $student->level === \App\Enums\StudentLevel::ThirdYear 
                    ? __('Announce Internship') 
                    : __('Create Apprenticeship'),
                'status' => $hasAgreement ? 'completed' : ($hasApplications ? 'current' : 'pending'),
            ],
            [
                'title' => $student->level === \App\Enums\StudentLevel::ThirdYear 
                    ? __('Get your Internship Agreement') 
                    : __('Get your Apprenticeship Agreement'),
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
            $student = auth()->user();
            
            // Check student level and redirect accordingly
            if ($student->level === \App\Enums\StudentLevel::FirstYear || 
                $student->level === \App\Enums\StudentLevel::SecondYear) {
                
                // For first and second year students, redirect to apprenticeship
                $hasApprenticeshipAgreement = \App\Models\Apprenticeship::where('student_id', $student->id)->exists();
                
                if ($hasApprenticeshipAgreement) {
                    return redirect()->route('filament.app.resources.apprenticeships.index');
                }
                
                return redirect()->route('filament.app.resources.apprenticeships.create');
            }
            
            // For third year students, redirect to final year internship agreements
            if ($this->hasAgreement) {
                return redirect()->route('filament.app.resources.final-year-internship-agreements.index');
            }

            return redirect()->route('filament.app.resources.final-year-internship-agreements.create');
        }

        // Optionally, redirect to the login page if not authenticated
        return redirect()->route('login');
    }
}
