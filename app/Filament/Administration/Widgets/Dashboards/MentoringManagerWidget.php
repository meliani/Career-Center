<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums;
use App\Models\Professor;
use App\Models\Project;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class MentoringManagerWidget extends Widget
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

    public $perPage = 9;

    public $loadMoreCount = 9;

    public $search = '';

    public $showSearch = false;

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
            ->select('projects.*')
            ->with([
                'professors' => function ($query) {
                    $query->select(['users.id as user_id', 'users.name', 'professor_projects.jury_role', 'professor_projects.professor_id'])
                        ->withPivot('jury_role');
                },
                'final_internship_agreements' => function ($query) {
                    $query->select([
                        'final_year_internship_agreements.id as agreement_id',
                        'final_year_internship_agreements.organization_id',
                    ])
                        ->with(['organization' => function ($q) {
                            $q->select('organizations.id as organization_id', 'organizations.name');
                        }]);
                },
            ])
            ->whereHas('final_internship_agreements', function ($query) {
                if (auth()->user()->hasRole(Enums\Role::DepartmentHead)) {
                    $query->where('assigned_department', auth()->user()->department);
                }
            });

        // Add search functionality with optimization
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('final_internship_agreements.student', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm);
                })
                    ->orWhereHas('final_internship_agreements.organization', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Optimize filter queries
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

        $projects = $query->latest('id')
            ->take($this->perPage)
            ->get();

        // Pre-calculate professor assignments for each project
        foreach ($projects as $project) {
            $professorsByRole = $project->professors->groupBy('pivot.jury_role');

            $project->has_supervisor = isset($professorsByRole[Enums\JuryRole::Supervisor->value]);
            $project->has_first_reviewer = isset($professorsByRole[Enums\JuryRole::FirstReviewer->value]);
            $project->has_second_reviewer = isset($professorsByRole[Enums\JuryRole::SecondReviewer->value]);

            $project->supervisor_id = $project->has_supervisor ?
                $professorsByRole[Enums\JuryRole::Supervisor->value]->first()->id : null;
            $project->first_reviewer_id = $project->has_first_reviewer ?
                $professorsByRole[Enums\JuryRole::FirstReviewer->value]->first()->id : null;
            $project->second_reviewer_id = $project->has_second_reviewer ?
                $professorsByRole[Enums\JuryRole::SecondReviewer->value]->first()->id : null;
        }

        $this->projects = $projects;
    }

    public function loadMore()
    {
        $this->perPage += $this->loadMoreCount;
        $this->loadProjects();
    }

    public function updatedSearch()
    {
        $this->perPage = $this->loadMoreCount; // Reset pagination when searching
        $this->loadProjects();
    }

    public function updatedShowSearch()
    {
        if (! $this->showSearch) {
            $this->search = '';
            $this->perPage = $this->loadMoreCount;
            $this->loadProjects();
        }
    }

    public function toggleSearch()
    {
        $this->showSearch = ! $this->showSearch;
        if (! $this->showSearch) {
            $this->search = '';
            $this->loadProjects();
        }
    }

    protected function loadProfessors()
    {
        $currentYearId = \App\Models\Year::current()->id;

        $query = Professor::query();

        // Scope professors based on user role
        if (auth()->user()->hasRole(Enums\Role::DepartmentHead)) {
            $query->where('department', auth()->user()->department);
        }
        // No department filter for administrators - they see all professors

        $this->departmentProfessors = $query
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
                // Scope stats based on user role
                if (auth()->user()->hasRole(Enums\Role::DepartmentHead)) {
                    $query->where('assigned_department', auth()->user()->department);
                }
                // No department filter for administrators - they see all stats
            });

        // Apply search filter to base query if search is active
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $baseQuery->where(function ($q) use ($searchTerm) {
                $q->whereHas('final_internship_agreements.student', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm);
                })
                    ->orWhereHas('final_internship_agreements.organization', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm);
                    });
            });
        }

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
        return auth()->user()->isAdministrator() ||
               auth()->user()->hasRole(Enums\Role::DepartmentHead);
    }

    public function render(): \Illuminate\View\View
    {
        $this->loadProjects();  // Move loading to render
        $this->loadProfessors(); // Move loading to render

        return view('livewire.department-project-assignments');
    }
}
