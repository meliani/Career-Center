<?php

namespace App\Providers;

use App\Listeners\UpdateUserLastLoginAt;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Project;
use App\Observers\AgreementObserver;
use App\Observers\ProjectObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UpdateUserLastLoginAt::class,
        ],
    ];

    protected $observers = [
        Project::class => [ProjectObserver::class],
        FinalYearInternshipAgreement::class => [AgreementObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Project::observe(ProjectObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
