<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\InternshipOffer;
use App\Models\Professor;
use App\Models\Project;
use App\Models\TimeTable;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\FinalYearInternshipAgreementPolicy;
use App\Policies\InternshipAgreementPolicy;
use App\Policies\InternshipOfferPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TimetablePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $administrators = [Role::SuperAdministrator, Role::Administrator, Role::Direction];

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
        FinalYearInternshipAgreement::class => FinalYearInternshipAgreementPolicy::class,
        TimeTable::class => TimeTablePolicy::class,
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

        // Remove the custom Gates for statistics
        // Gate::define('view-statistics', function (User $user) {
        //     return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        // });
        // Gate::define('view-offers-statistics', function (User $user) {
        //     return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        // });
        // Gate::define('view-applications-statistics', function (User $user) {
        //     return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        // });
        // Gate::define('view-agreements-statistics', function (User $user) {
        //     return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        // });
        // Gate::define('view-users-statistics', function (User $user) {
        //     return $user->hasAnyRole($this->administrators);
        // });
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
        Gate::define('authorize-defense', [ProjectPolicy::class, 'authorizeDefense']);
        Gate::define('send-defense-email', [ProjectPolicy::class, 'sendDefenseEmail']);
        Gate::define('manage-planning', [TimetablePolicy::class, 'update']);
        Gate::define('manage-supervision', [ProjectPolicy::class, 'update']);
        Gate::define('manage-project', [ProjectPolicy::class, 'update']);
        Gate::define('manage-timetables', fn (User $user) => ($user->isAdministrator()));

        Gate::define('manage-projects', fn (User $user) => ($user->isAdministrator() || $user->isAdministrativeSupervisor()));
        Gate::define('manage-students', fn (User $user) => ($user->isAdministrator()));
        Gate::define('create-token', fn (User $user) => $user->isSuperAdministrator());
        Gate::define('view-statistics', function (User $user) {
            return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        });

        Gate::define('view-offers-statistics', function (User $user) {
            return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        });

        Gate::define('view-applications-statistics', function (User $user) {
            return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        });

        Gate::define('view-agreements-statistics', function (User $user) {
            return $user->hasAnyRole(array_merge($this->administrators, $this->professors));
        });

        Gate::define('view-users-statistics', function (User $user) {
            return $user->hasAnyRole($this->administrators);
        });

        Gate::define('view env editor', function (User $user) {
            return $user->hasRole(Role::SuperAdministrator) && auth()->user()->id === 1;
        });

        Gate::define('view backups', function (User $user) {
            return $user->hasRole(Role::SuperAdministrator) && auth()->user()->id === 1;
        });
    }
}
