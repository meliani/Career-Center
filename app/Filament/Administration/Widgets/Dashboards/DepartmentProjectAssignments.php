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

    public $activeFilter = 'all'; // Default filter

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function mount()
    {
        // Empty mount or other initialization if needed
    }

    public function setFilter($filter)
    {
        $this->activeFilter = $filter;
        $this->loadProjects();
    }

    protected function loadProjects()
    {
        $query = Project::query()
            ->whereHas('final_internship_agreements', function ($query) {
                $query->where('assigned_department', auth()->user()->department);
            });

        // Apply filters
        switch ($this->activeFilter) {
            case 'pendingSupervisor':
                $query->whereDoesntHave('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                });

                break;

            case 'pendingReviewers':
                $query->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                })->where(function ($query) {
                    $query->whereDoesntHave('professors', function ($q) {
                        $q->where('jury_role', Enums\JuryRole::FirstReviewer);
                    })->orWhereDoesntHave('professors', function ($q) {
                        $q->where('jury_role', Enums\JuryRole::SecondReviewer);
                    });
                });

                break;

            case 'assigned':
                $query->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                })->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::FirstReviewer);
                })->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::SecondReviewer);
                });

                break;
        }

        $this->projects = $query->with(['professors', 'final_internship_agreements.organization'])->get();
    }

    protected function loadProfessors()
    {
        $currentYearId = \App\Models\Year::current()->id;

        $this->departmentProfessors = Professor::query()
            ->where('department', auth()->user()->department)
            ->withCount(['activeProjects as supervisor_count' => function ($query) {
                $query->where('jury_role', Enums\JuryRole::Supervisor);
            }])
            ->withCount(['activeProjects as reviewer_count' => function ($query) {
                $query
                    ->where('jury_role', Enums\JuryRole::FirstReviewer)
                    ->orWhere('jury_role', Enums\JuryRole::SecondReviewer);
            }])
            ->orderBy('name')
            ->get();
    }

    protected function validateProfessorAssignment(Project $project, $professorId, $role)
    {
        // Check if professor is already assigned to another role in this project
        $existingRole = $project->professors()
            ->where('professor_id', $professorId)
            ->whereNotIn('jury_role', [$role])
            ->first();

        if ($existingRole) {
            $this->addError('assignment', __('This professor is already assigned as :role in this project', [
                'role' => strtolower(__($existingRole->pivot->jury_role->getLabel())),
            ]));

            return false;
        }

        return true;
    }

    public function assignSupervisor($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId && ! $this->validateProfessorAssignment($project, $professorId, Enums\JuryRole::Supervisor)) {
                return;
            }

            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::Supervisor)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::Supervisor,
                    'created_by' => auth()->id(),
                ]);
            } else {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::Supervisor)->detach();
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
            }

            $this->loadProjects(); // Load fresh project data
            $this->loadProfessors(); // Add this line to reload professor counts

            $hasSupervisor = $project->professors()->wherePivot('jury_role', Enums\JuryRole::Supervisor)->exists();
            $this->dispatch('supervisor-assigned', projectId: $projectId, exists: $hasSupervisor);
        }
    }

    public function assignFirstReviewer($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId && ! $this->validateProfessorAssignment($project, $professorId, Enums\JuryRole::FirstReviewer)) {
                return;
            }

            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::FirstReviewer,
                    'created_by' => auth()->id(),
                ]);
                $this->dispatch('reviewer-assigned', projectId: $projectId, exists: true);
            } else {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::FirstReviewer)->detach();
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
                $this->dispatch('reviewer-assigned', projectId: $projectId, exists: false);
            }

            $this->loadProjects();
            $this->loadProfessors(); // Add this line to reload professor counts
        }
    }

    public function assignSecondReviewer($projectId, $professorId)
    {
        $project = Project::find($projectId);
        if ($project) {
            if ($professorId && ! $this->validateProfessorAssignment($project, $professorId, Enums\JuryRole::SecondReviewer)) {
                return;
            }

            if ($professorId) {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
                $project->professors()->attach($professorId, [
                    'jury_role' => Enums\JuryRole::SecondReviewer,
                    'created_by' => auth()->id(),
                ]);
                $this->dispatch('reviewer-assigned', [
                    'projectId' => $projectId,
                    'exists' => true,
                ]);
            } else {
                $project->professors()->wherePivot('jury_role', Enums\JuryRole::SecondReviewer)->detach();
                $this->dispatch('reviewer-assigned', [
                    'projectId' => $projectId,
                    'exists' => false,
                ]);
            }

            $this->loadProjects();
            $this->loadProfessors(); // Add this line to reload professor counts
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
            'pendingSupervisor' => (clone $baseQuery)
                ->whereDoesntHave('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                })->count(),
            'pendingReviewers' => (clone $baseQuery)
                ->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('professors', function ($q) {
                        $q->where('jury_role', Enums\JuryRole::FirstReviewer);
                    })->orWhereDoesntHave('professors', function ($q) {
                        $q->where('jury_role', Enums\JuryRole::SecondReviewer);
                    });
                })->count(),
            'assigned' => (clone $baseQuery)
                ->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::Supervisor);
                })
                ->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::FirstReviewer);
                })
                ->whereHas('professors', function ($q) {
                    $q->where('jury_role', Enums\JuryRole::SecondReviewer);
                })->count(),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole(Enums\Role::DepartmentHead);
    }

    public function render(): \Illuminate\View\View
    {
        $this->loadProjects();  // Move loading to render
        $this->loadProfessors(); // Move loading to render

        return view('livewire.department-project-assignments');
    }
}
