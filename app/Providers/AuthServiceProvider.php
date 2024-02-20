<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Enums\Role;
use App\Models\InternshipAgreement;
use App\Models\User;
use App\Policies\ActivityPolicy;
// use Illuminate\Auth\Access\Response;
use App\Policies\InternshipAgreementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $administrators = [Role::SuperAdministrator, Role::Administrator];

    protected $professors = [Role::Professor, Role::ProgramCoordinator, Role::DepartmentHead];

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
        Gate::define('view-internship', function (User $user, InternshipAgreement $internship) {
            // Check if the user is an administrator
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }

            return $internship->student->program === $user->assigned_program;
        });
        Gate::define('batch-assign-internships-to-projects', function (User $user) {
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }

            return false;

            // return $internship->student->program === $user->assigned_program;
        });
        Gate::define('validate-internship', [InternshipAgreementPolicy::class, 'update']);
        Gate::define('sign-internship', [InternshipAgreementPolicy::class, 'update']);

        /* Gate::define('viewPulse', function (User $user) {
            return $user->role === Role::SuperAdministrator;
        }); */

        // Gate::define('viewAny-internship', function ($user, $internship) {
        //     // Check if the user is an administrator
        //     if ($user->hasAnyRole($this->administrators)) {
        //         return true;
        //     }

        //     // Check if the user is a professor and the internship's student's program matches the user's program
        //     if ($user->hasAnyRole($this->professors) && $internship->student->program == $user->assigned_program) {
        //         return true;
        //     }

        //     return false;
        // });
        $this->registerPolicies();
    }
}
