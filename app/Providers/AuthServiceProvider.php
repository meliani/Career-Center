<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Internship;
use App\Policies\InternshipPolicy;
use Illuminate\Support\Facades\Gate;
// use Illuminate\Auth\Access\Response;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use App\Policies\ActivityPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $administrators = ['SuperAdministrator', 'Administrator'];
    protected $professors = ['Professor', 'HeadOfDepartment', 'ProgramCoordinator'];
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('view-internship', function (User $user, Internship $internship) {
            // Check if the user is an administrator
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }
            return $internship->student->filiere_text === $user->program_coordinator;
        });
        Gate::define('review-internship', [InternshipPolicy::class, 'update']);
        
        Gate::define('viewPulse', function (User $user) {
            return $user->role === 'SuperAdministrator';
        });
        // Gate::define('viewAny-internship', function ($user, $internship) {
        //     // Check if the user is an administrator
        //     if ($user->hasAnyRole($this->administrators)) {
        //         return true;
        //     }

        //     // Check if the user is a professor and the internship's student's program matches the user's program
        //     if ($user->hasAnyRole($this->professors) && $internship->student->filiere_text == $user->program_coordinator) {
        //         return true;
        //     }

        //     return false;
        // });
        $this->registerPolicies();
    }
}
