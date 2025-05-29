<?php

namespace App\Services;

use App\Enums\Department;
use App\Enums\JuryRole;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use App\Models\Year;
use Illuminate\Support\Collection;

class ProjectStatisticsService
{
    protected Year $year;
    protected ?Department $departmentFilter = null;

    public function __construct(?Year $year = null)
    {
        $this->year = $year ?? Year::current();
    }

    /**
     * Set department filter for calculations
     */
    public function forDepartment(?Department $department): self
    {
        $this->departmentFilter = $department;
        return $this;
    }

    /**
     * Get comprehensive department statistics
     */
    public function getDepartmentStatistics(?Department $specificDepartment = null): Collection
    {
        $departments = $specificDepartment 
            ? collect([$specificDepartment])
            : collect(Department::cases());

        return $departments->map(function ($department) {
            return [
                'department' => $department,
                'department_name' => $department->getLabel(),
                'department_code' => $department->value,
                'professors_count' => $this->getProfessorsCount($department),
                'projects_by_assigned_department' => $this->getProjectsByAssignedDepartment($department),
                'projects_by_supervisor_department' => $this->getProjectsBySupervisorDepartment($department),
                'projects_by_professor_department' => $this->getProjectsByProfessorDepartment($department),
                'mentoring_stats' => $this->getMentoringStatistics($department),
                'supervision_distribution' => $this->getSupervisionDistribution($department),
                'reviewing_distribution' => $this->getReviewingDistribution($department),
            ];
        });
    }

    /**
     * Get total professors count for a department
     */
    public function getProfessorsCount(Department $department): int
    {
        return Professor::where('department', $department->value)->count();
    }

    /**
     * Get projects count by assigned_department field (initial assignment)
     */
    public function getProjectsByAssignedDepartment(Department $department): int
    {
        return FinalYearInternshipAgreement::where('assigned_department', $department->value)
            ->where('year_id', $this->year->id)
            ->count();
    }

    /**
     * Get projects count by supervisor's department (actual supervision)
     */
    public function getProjectsBySupervisorDepartment(Department $department): int
    {
        return Project::whereHas('final_internship_agreements', function ($query) {
                $query->where('year_id', $this->year->id);
            })
            ->whereHas('professors', function ($query) use ($department) {
                $query->where('department', $department->value)
                      ->where('professor_projects.jury_role', JuryRole::Supervisor->value);
            })
            ->count();
    }

    /**
     * Get projects count by any professor's department (supervision + reviewing)
     */
    public function getProjectsByProfessorDepartment(Department $department): int
    {
        return Project::whereHas('final_internship_agreements', function ($query) {
                $query->where('year_id', $this->year->id);
            })
            ->whereHas('professors', function ($query) use ($department) {
                $query->where('department', $department->value);
            })
            ->count();
    }

    /**
     * Get detailed mentoring statistics for a department
     */
    public function getMentoringStatistics(Department $department): array
    {
        $professors = Professor::where('department', $department->value)->get();
        
        if ($professors->isEmpty()) {
            return [
                'professors_count' => 0,
                'avg_supervising' => 0,
                'avg_reviewing' => 0,
                'total_avg' => 0,
                'supervision_counts' => [],
                'reviewing_counts' => [],
            ];
        }

        $supervisionCounts = [];
        $reviewingCounts = [];

        foreach ($professors as $professor) {
            // Count supervisions
            $supervisions = $professor->projects()
                ->wherePivot('jury_role', JuryRole::Supervisor->value)
                ->whereHas('final_internship_agreements', function($query) {
                    $query->where('year_id', $this->year->id);
                })
                ->count();

            // Count reviews (both first and second reviewer roles)
            $reviews = $professor->projects()
                ->whereIn('professor_projects.jury_role', [
                    JuryRole::FirstReviewer->value, 
                    JuryRole::SecondReviewer->value
                ])
                ->whereHas('final_internship_agreements', function($query) {
                    $query->where('year_id', $this->year->id);
                })
                ->count();

            $supervisionCounts[] = $supervisions;
            $reviewingCounts[] = $reviews;
        }

        $avgSupervising = count($supervisionCounts) > 0 ? round(array_sum($supervisionCounts) / count($supervisionCounts), 2) : 0;
        $avgReviewing = count($reviewingCounts) > 0 ? round(array_sum($reviewingCounts) / count($reviewingCounts), 2) : 0;
        $totalAvg = round($avgSupervising + $avgReviewing, 2);

        return [
            'professors_count' => $professors->count(),
            'avg_supervising' => $avgSupervising,
            'avg_reviewing' => $avgReviewing,
            'total_avg' => $totalAvg,
            'supervision_counts' => $supervisionCounts,
            'reviewing_counts' => $reviewingCounts,
        ];
    }

    /**
     * Get supervision distribution (which departments supervise projects initially assigned to this department)
     */
    public function getSupervisionDistribution(Department $department): Collection
    {
        return collect(Department::cases())->map(function ($supervisorDept) use ($department) {
            $count = Project::whereHas('final_internship_agreements', function ($query) use ($department) {
                    $query->where('year_id', $this->year->id)
                          ->where('assigned_department', $department->value);
                })
                ->whereHas('professors', function ($query) use ($supervisorDept) {
                    $query->where('department', $supervisorDept->value)
                          ->where('professor_projects.jury_role', JuryRole::Supervisor->value);
                })
                ->count();

            return [
                'department' => $supervisorDept,
                'count' => $count,
            ];
        })->filter(fn($item) => $item['count'] > 0);
    }

    /**
     * Get reviewing distribution (which departments review projects initially assigned to this department)
     */
    public function getReviewingDistribution(Department $department): Collection
    {
        return collect(Department::cases())->map(function ($reviewerDept) use ($department) {
            $count = Project::whereHas('final_internship_agreements', function ($query) use ($department) {
                    $query->where('year_id', $this->year->id)
                          ->where('assigned_department', $department->value);
                })
                ->whereHas('professors', function ($query) use ($reviewerDept) {
                    $query->where('department', $reviewerDept->value)
                          ->whereIn('professor_projects.jury_role', [
                              JuryRole::FirstReviewer->value,
                              JuryRole::SecondReviewer->value
                          ]);
                })
                ->count();

            return [
                'department' => $reviewerDept,
                'count' => $count,
            ];
        })->filter(fn($item) => $item['count'] > 0);
    }

    /**
     * Get summary statistics for all departments
     */
    public function getSummaryStatistics(): array
    {
        $totalProjects = Project::whereHas('final_internship_agreements', function ($query) {
            $query->where('year_id', $this->year->id);
        })->count();

        $totalProfessors = Professor::count();
        $totalAgreements = FinalYearInternshipAgreement::where('year_id', $this->year->id)->count();

        return [
            'year' => $this->year->title,
            'total_projects' => $totalProjects,
            'total_professors' => $totalProfessors,
            'total_agreements' => $totalAgreements,
        ];
    }

    /**
     * Get department mismatch analysis
     */
    public function getDepartmentMismatches(): Collection
    {
        return collect(Department::cases())->map(function ($department) {
            $assignedCount = $this->getProjectsByAssignedDepartment($department);
            $supervisedCount = $this->getProjectsBySupervisorDepartment($department);
            $professorCount = $this->getProjectsByProfessorDepartment($department);
            
            return [
                'department' => $department,
                'assigned_department_count' => $assignedCount,
                'supervisor_department_count' => $supervisedCount,
                'professor_department_count' => $professorCount,
                'has_assignment_mismatch' => $assignedCount !== $professorCount,
                'assignment_difference' => $professorCount - $assignedCount,
                'has_supervision_mismatch' => $assignedCount !== $supervisedCount,
                'supervision_difference' => $supervisedCount - $assignedCount,
            ];
        })->filter(fn($item) => $item['has_assignment_mismatch'] || $item['has_supervision_mismatch']);
    }

    /**
     * Get mentoring statistics sorted by total average (for display)
     */
    public function getMentoringStatisticsSorted(): Collection
    {
        return $this->getDepartmentStatistics()
            ->map(function ($dept) {
                $stats = $dept['mentoring_stats'];
                return [
                    'department' => $dept['department'],
                    'department_name' => $dept['department_name'],
                    'professors_count' => $stats['professors_count'],
                    'avg_supervising' => $stats['avg_supervising'],
                    'avg_reviewing' => $stats['avg_reviewing'],
                    'total_avg' => $stats['total_avg'],
                ];
            })
            ->sortByDesc('total_avg')
            ->values();
    }

    /**
     * Get widget-compatible statistics
     */
    public function getWidgetStatistics(bool $useAlternativeCalculation = false): Collection
    {
        return collect(Department::cases())->map(function ($department) use ($useAlternativeCalculation) {
            if ($useAlternativeCalculation) {
                // Use professor department calculation (matches integrity check)
                $projectCount = $this->getProjectsByProfessorDepartment($department);
            } else {
                // Use assigned department calculation (original widget behavior)
                $projectCount = $this->getProjectsByAssignedDepartment($department);
            }

            $professorsCount = $this->getProfessorsCount($department);
            $ratio = $professorsCount ? $projectCount / $professorsCount : 0;

            return [
                'name' => $department->getLabel(),
                'description' => $department->getDescription(),
                'count' => $projectCount,
                'color' => $department->getColor(),
                'icon' => $department->getIcon(),
                'ratio' => $ratio,
                'professors_count' => $professorsCount,
                'department' => $department,
            ];
        })->sortByDesc('count');
    }

    /**
     * Get integrity check issues
     */
    public function getIntegrityIssues(): array
    {
        $issues = [];
        $mismatches = $this->getDepartmentMismatches();

        foreach ($mismatches as $mismatch) {
            if ($mismatch['has_assignment_mismatch']) {
                $issues[] = [
                    'type' => 'Department Assignment Mismatch',
                    'description' => sprintf(
                        'Department %s: Projects by assigned_department (%d) != Projects by professor department (%d)',
                        $mismatch['department']->value,
                        $mismatch['assigned_department_count'],
                        $mismatch['professor_department_count']
                    ),
                    'severity' => 'warning',
                    'entity_type' => 'department',
                    'entity_id' => $mismatch['department']->value,
                    'difference' => $mismatch['assignment_difference']
                ];
            }
        }

        return $issues;
    }
}
