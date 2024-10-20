<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\InternshipAgreement;
use App\Models\InternshipOffer;
use App\Models\Professor;
use App\Models\Project;
use App\Models\User;
// use Illuminate\Auth\Access\Response;
use App\Policies\ActivityPolicy;
use App\Policies\InternshipAgreementPolicy;
use App\Policies\InternshipOfferPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\ProjectPolicy;
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
        Project::class => ProjectPolicy::class,
        Professor::class => ProfessorPolicy::class,
        InternshipAgreement::class => InternshipAgreementPolicy::class,
        InternshipOffer::class => InternshipOfferPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('see-sent-emails', function (User $user) {
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }
        });
        $this->registerInternshipAgreementPolicies();
        // $this->registerPulsesPolicy();
        $this->registerPolicies();

        $this->registerProjectPolicies();
    }

    private function registerInternshipAgreementPolicies()
    {

        Gate::define('view-internship', function (User $user, InternshipAgreement $internship) {
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
        });
        Gate::define('validate-internship', [InternshipAgreementPolicy::class, 'update']);
        Gate::define('authorize-defense', [ProjectPolicy::class, 'update']);
        Gate::define('sign-internship', [InternshipAgreementPolicy::class, 'update']);
    }

    private function registerPulsesPolicy()
    {
        Gate::define('viewPulse', function (User $user) {
            return $user->role === Role::SuperAdministrator;
        });
    }

    private function registerProjectPolicies()
    {
        Gate::define('view-project', function (User $user, Project $project) {
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }

            return $project->program === $user->assigned_program;
        });
        Gate::define('assign-project-to-professor', function (User $user) {
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }

            return false;
        });
        Gate::define('assign-project-to-student', function (User $user) {
            if ($user->hasAnyRole($this->administrators)) {
                return true;
            }

            return false;
        });
        Gate::define('validate-project', [ProjectPolicy::class, 'update']);
        Gate::define('authorize-defense', [ProjectPolicy::class, 'update']);
        Gate::define('send-defense-email', [ProjectPolicy::class, 'update']);

        Gate::define('manage-projects', fn (User $user) => ($user->isAdministrator() || $user->isAdministrativeSupervisor()));
        Gate::define('manage-students', fn (User $user) => ($user->isAdministrator()));
        Gate::define('create-token', fn (User $user) => $user->isSuperAdministrator());

    }
}
