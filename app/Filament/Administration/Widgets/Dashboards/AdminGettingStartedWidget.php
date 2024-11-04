<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Models\InternshipApplication as Application;
use App\Models\InternshipOffer;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\Widget;

class AdminGettingStartedWidget extends Widget
{
    protected static string $view = 'filament.administration.widgets.dashboards.admin-getting-started-widget';

    protected static ?int $sort = 1;

    public array $statistics = [];

    public array $recentActivities = [];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadRecentActivities();
    }

    protected function loadStatistics()
    {
        $this->statistics = [
            'total_users' => User::count() + Student::count(),
            'new_applications' => Application::whereDate('created_at', today())
                ->count(),
            'new_offers' => InternshipOffer::active()->count(),
            'applications' => Application::count(),
            'active_users' => User::whereDate('last_login_at', '>=', now()->subDays(7))->count() + Student::whereDate('last_login_at', '>=', now()->subDays(7))->count(),
        ];
    }

    protected function loadRecentActivities()
    {
        $this->recentActivities = Application::latest()
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'title' => "New application from {$item->student?->name}",
                'time' => $item->created_at->diffForHumans(),
                'status' => $item->status,
            ])
            ->toArray();
    }
}
