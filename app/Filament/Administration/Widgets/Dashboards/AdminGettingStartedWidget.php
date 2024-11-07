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
            'total_users' => [
                'label' => __('Total platform Users'),
                'value' => User::count() + Student::count(),
            ],
            'new_applications' => [
                'label' => __('New Applications') . ' (' . __('Today') . ')',
                'value' => Application::whereDate('created_at', today())->count(),
            ],
            'new_offers' => [
                'label' => __('New Internship Offers'),
                'value' => InternshipOffer::published()->count(),
            ],
            'pending_offers' => [
                'label' => __('Internship Offers Pending Approval'),
                'value' => InternshipOffer::submitted()->count(),
            ],
            'applications' => [
                'label' => __('Applications'),
                'value' => Application::count(),
            ],
            'active_users' => [
                'label' => __('Active Users') . ' (' . __('Last 7 days') . ')',
                'value' => User::whereDate('last_login_at', '>=', now()->subDays(7))->count() + Student::whereDate('last_login_at', '>=', now()->subDays(7))->count(),
            ],
        ];
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
