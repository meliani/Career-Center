<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums;
use App\Models\Professor;
use App\Models\Project;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class DepartmentProjectAssignments extends Widget
{
    // Add polling
    #[Computed]
    public function pooling()
    {
        return '30s';
    }

    public $projects;

    public $departmentProfessors;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function mount()
    {
        $this->loadProjects();
        $this->loadProfessors();
    }

    protected function loadProjects()
    {
        $this->projects = Project::query()
            ->whereHas('final_internship_agreements', function ($query) {
                $query->where('assigned_department', auth()->user()->department);
            })
            ->with(['professors', 'final_internship_agreements.organization'])
            ->get();
    }

    protected function loadProfessors()
    {
        $this->departmentProfessors = Professor::query()
            ->where('department', auth()->user()->department)
            ->orderBy('name')
            ->get();
    }

    public function assignSupervisor($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::Supervisor)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::Supervisor,
                    'created_by' => auth()->id(),
                ]);
            } else {
                // If professorId is empty, just detach the supervisor
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::Supervisor)->detach();
                // Also detach reviewers since they can't exist without a supervisor
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
            }
            $this->loadProjects();
        }
    }

    public function assignFirstReviewer($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::FirstReviewer,
                    'created_by' => auth()->id(),
                ]);
            } else {
                // If professorId is empty, just detach the first reviewer
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                // Also detach second reviewer since it can't exist without a first reviewer
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
            }
            $this->loadProjects();
        }
    }

    public function assignSecondReviewer($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::SecondReviewer,
                    'created_by' => auth()->id(),
                ]);
            } else {
                // If professorId is empty, just detach the second reviewer
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
            }
            $this->loadProjects();
        }
    }

    #[Computed]
    public function stats()
    {
        $baseQuery = Project::query()
            ->whereHas('final_internship_agreements', function ($query) {
                $query->where('assigned_department', auth()->user()->department);
            });

        return [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery
                ->whereDoesntHave('professors', function ($query) {
                    $query->whereIn('jury_role', [
                        Enums\JuryRole::Supervisor,
                        Enums\JuryRole::FirstReviewer,
                        Enums\JuryRole::SecondReviewer,
                    ]);
                })->count(),
            'assigned' => $baseQuery
                ->whereHas('professors', function ($query) {
                    $query->where('jury_role', Enums\JuryRole::Supervisor);
                })
                ->whereHas('professors', function ($query) {
                    $query->whereIn('jury_role', [
                        Enums\JuryRole::FirstReviewer,
                        Enums\JuryRole::SecondReviewer,
                    ]);
                })->count(),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole(Enums\Role::DepartmentHead);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.department-project-assignments');
    }
}
