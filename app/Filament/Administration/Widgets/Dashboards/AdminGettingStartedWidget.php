<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipApplication as Application;
use App\Models\InternshipOffer;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AdminGettingStartedWidget extends Widget
{
    protected static string $view = 'filament.administration.widgets.dashboards.admin-getting-started-widget';

    protected static ?int $sort = -1;

    public array $statistics = [];

    public array $recentActivities = [];

    public string $selectedTrendPeriod = 'month';

    // Remove this as it's not needed with wire:model.live
    // protected $listeners = ['refreshComponent' => '$refresh'];

    // Change visibility to public for the trend periods
    public array $trendPeriods = [
        'week' => 'Past 7 days',
        'month' => 'Past 30 days',
        'quarter' => 'Past 3 months',
        'year' => 'Past 12 months',
    ];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadRecentActivities();
    }

    protected function calculateTrend($currentCount, $previousCount): int
    {
        if ($previousCount == 0) {
            return 0;
        }

        return round((($currentCount - $previousCount) / $previousCount) * 100);
    }

    // Make method public to be accessible by Livewire
    public function getTrendPeriodDates(): array
    {
        return match ($this->selectedTrendPeriod) {
            'week' => [
                'current' => now()->startOfWeek(),
                'previous' => now()->subWeek()->startOfWeek(),
            ],
            'month' => [
                'current' => now()->startOfMonth(),
                'previous' => now()->subMonth()->startOfMonth(),
            ],
            'quarter' => [
                'current' => now()->startOfQuarter(),
                'previous' => now()->subQuarter()->startOfQuarter(),
            ],
            'year' => [
                'current' => now()->startOfYear(),
                'previous' => now()->subYear()->startOfYear(),
            ],
        };
    }

    protected function getPeriodLabel(): string
    {
        return match ($this->selectedTrendPeriod) {
            'week' => __('vs previous week'),
            'month' => __('vs previous month'),
            'quarter' => __('vs previous quarter'),
            'year' => __('vs previous year'),
        };
    }

    protected function loadStatistics()
    {
        $user = Auth::user();
        $period = $this->getTrendPeriodDates();
        $currentPeriodStart = $period['current'];
        $previousPeriodStart = $period['previous'];

        // Calculate applications trend
        $currentApplications = Application::where('created_at', '>=', $currentPeriodStart)->count();
        $previousApplications = Application::whereBetween('created_at', [$previousPeriodStart, $currentPeriodStart])->count();

        // Calculate offers trend
        $currentOffers = InternshipOffer::active()->where('created_at', '>=', $currentPeriodStart)->count();
        $previousOffers = InternshipOffer::active()->whereBetween('created_at', [$previousPeriodStart, $currentPeriodStart])->count();

        // Calculate agreements trend using FinalYearInternshipAgreement
        $currentAgreements = FinalYearInternshipAgreement::where('created_at', '>=', $currentPeriodStart)->count();
        $previousAgreements = FinalYearInternshipAgreement::whereBetween('created_at', [$previousPeriodStart, $currentPeriodStart])->count();

        // Calculate signed agreements trend
        $currentSignedAgreements = FinalYearInternshipAgreement::where('status', 'Signed')
            ->where('signed_at', '>=', $currentPeriodStart)
            ->count();
        $previousSignedAgreements = FinalYearInternshipAgreement::where('status', 'Signed')
            ->whereBetween('signed_at', [$previousPeriodStart, $currentPeriodStart])
            ->count();

        $comparisonLabel = $this->getPeriodLabel();

        $this->statistics = [
            'new_offers' => [
                'key' => 'new_offers',
                'model_class' => InternshipOffer::class,
                'label' => __('Active Internship Offers'),
                'value' => InternshipOffer::active()->count(),
                'color' => 'success',
                'description' => __('Currently available positions') . " ({$comparisonLabel})",
                'trend' => $this->calculateTrend($currentOffers, $previousOffers),
                'route' => $user->can('viewAny', InternshipOffer::class) ? route('filament.Administration.resources.internship-offers.index') : null,
            ],
            'pending_offers' => [
                'key' => 'pending_offers',
                'model_class' => InternshipOffer::class,
                'can' => 'view-offers-statistics',
                'label' => __('Pending Review'),
                'value' => InternshipOffer::submitted()->count(),
                'color' => 'warning',
                'description' => __('Internship offers awaiting approval'),
                'route' => $user->can('viewAny', InternshipOffer::class) ? route('filament.Administration.resources.internship-offers.index', ['tableFilters[status][values][0]' => 'Submitted']) : null,
            ],
            'applications' => [
                'key' => 'applications',
                'model_class' => Application::class,
                'label' => __('Student Applications'),
                'value' => Application::count(),
                'color' => 'info',
                'description' => __('Total submitted applications') . " ({$comparisonLabel})",
                'trend' => $this->calculateTrend($currentApplications, $previousApplications),
                'route' => $user->can('viewAny', Application::class) ? route('filament.Administration.resources.applications.index') : null,
            ],
            'agreements' => [
                'key' => 'agreements',
                'model_class' => FinalYearInternshipAgreement::class,
                'label' => __('Active Agreements'),
                'value' => FinalYearInternshipAgreement::count(),
                'color' => 'success',
                'description' => __('Total internship agreements in process') . " ({$comparisonLabel})",
                'trend' => $this->calculateTrend($currentAgreements, $previousAgreements),
                'route' => $user->can('viewAny', FinalYearInternshipAgreement::class) ? route('filament.Administration.resources.final-year-internship-agreements.index') : null,
            ],
            'signed_agreements' => [
                'key' => 'signed_agreements',
                'model_class' => FinalYearInternshipAgreement::class,
                'can' => 'view-agreements-statistics',
                'label' => __('Completed Agreements'),
                'value' => FinalYearInternshipAgreement::where('status', 'Signed')->count(),
                'color' => 'primary',
                'description' => __('Fully signed and processed agreements') . " ({$comparisonLabel})",
                'trend' => $this->calculateTrend($currentSignedAgreements, $previousSignedAgreements),
                'route' => $user->can('viewAny', FinalYearInternshipAgreement::class) ? route('filament.Administration.resources.final-year-internship-agreements.index', ['tableFilters[status][value]' => 'Signed']) : null,
            ],
            'active_users' => [
                'key' => 'active_users',
                'model_class' => User::class,
                'can' => 'view-users-statistics',
                'label' => __('Active Platform Users'),
                'value' => User::whereDate('last_login_at', '>=', now()->subDays(7))->count() + Student::whereDate('last_login_at', '>=', now()->subDays(7))->count(),
                'color' => 'primary',
                'description' => __('Users who logged in during the last 7 days'),
                'route' => $user->can('viewAny', User::class) ? route('filament.Administration.resources.students.index') : null,
            ],
            'total_users' => [
                'key' => 'total_users',
                'model_class' => User::class,
                'can' => 'view-users-statistics',
                'label' => __('Total Users'),
                'value' => User::count() + Student::count(),
                'color' => 'secondary',
                'description' => __('All registered students, professors, and administrators'),
                'route' => $user->can('viewAny', User::class) ? route('filament.Administration.resources.users.index') : null,
            ],
        ];
    }

    // Add rerender trigger
    public function updatedSelectedTrendPeriod(): void
    {
        // No need for dispatch, just load new statistics
        $this->loadStatistics();
    }

    protected function loadRecentActivities()
    {
        $this->recentActivities = Application::latest()
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'title' => __(' New application from') . " {$item->student?->name}",
                'time' => $item->created_at->diffForHumans(),
                'status' => $item->status,
            ])
            ->toArray();
    }
}
