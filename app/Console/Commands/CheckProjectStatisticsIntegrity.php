<?php

namespace App\Console\Commands;

use App\Enums\Department;
use App\Enums\JuryRole;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use App\Models\Year;
use App\Services\ProjectStatisticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CheckProjectStatisticsIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:project-statistics-integrity 
                            {--year= : Specific year ID to check (optional, defaults to current year)}
                            {--department= : Specific department to check (optional)}
                            {--detailed : Show detailed breakdown for each department}
                            {--fix-orphans : Attempt to fix orphaned relationships}
                            {--export= : Export results to file (csv|json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the integrity of project statistics and related information around Project model including professor assignments, department distributions, and mentoring data';    /**
     * Statistics tracking
     */
    protected array $issues = [];
    protected array $summary = [];
    protected int $totalProjects = 0;
    protected int $totalProfessors = 0;
    protected ProjectStatisticsService $statisticsService;    /**
     * Execute the console command.
     */    public function handle()
    {
        $this->info(__('🔍 Starting Project Statistics Integrity Check...'));
        $this->newLine();

        // Get the year to check
        $year = $this->getYearToCheck();
        
        // Initialize the statistics service
        $this->statisticsService = new ProjectStatisticsService($year);
        
        $this->info(__('📅 Checking data for year:') . " {$year->title}");
        $this->newLine();

        // Initialize summary
        $this->summary = [
            'year' => $year->title,
            'total_projects' => 0,
            'total_professors' => 0,
            'total_agreements' => 0,
            'departments' => [],
            'integrity_issues' => [],
            'mentoring_statistics' => []
        ];

        // Run all integrity checks
        $this->checkProjectAgreementIntegrity($year);
        $this->checkProfessorAssignmentIntegrity($year);
        $this->checkDepartmentDistributionIntegrity($year);
        $this->checkMentoringStatisticsIntegrity($year);
        $this->checkOrphanedRelationships($year);
        $this->checkDataConsistency($year);

        // Display results
        $this->displayResults();

        // Handle export if requested
        if ($this->option('export')) {
            $this->exportResults();
        }

        // Fix orphans if requested
        if ($this->option('fix-orphans')) {
            $this->fixOrphanedRelationships();
        }        $this->newLine();
        $issueCount = count($this->issues);
        if ($issueCount === 0) {
            $this->info(__('✅ All integrity checks passed! No issues found.'));
        } else {
            $this->error(__('❌ Found :count integrity issues that need attention.', ['count' => $issueCount]));
        }

        return $issueCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Get the year to check based on options
     */
    protected function getYearToCheck(): Year
    {
        if ($yearId = $this->option('year')) {
            $year = Year::find($yearId);
            if (!$year) {
                $this->error("Year with ID {$yearId} not found.");
                exit(1);
            }
            return $year;
        }

        return Year::current();
    }

    /**
     * Check Project-Agreement relationship integrity
     */    protected function checkProjectAgreementIntegrity(Year $year): void
    {
        $this->info(__('🔗 Checking Project-Agreement relationship integrity...'));

        // Get all agreements for the year
        $agreements = FinalYearInternshipAgreement::where('year_id', $year->id)->get();
        $this->summary['total_agreements'] = $agreements->count();
        
        $projectsWithAgreements = 0;
        $agreementsWithoutProjects = 0;
        $duplicateProjectAssignments = 0;

        foreach ($agreements as $agreement) {
            // Check if agreement has a project
            $projectRelations = $agreement->project()->get();
            
            if ($projectRelations->isEmpty()) {
                $agreementsWithoutProjects++;
                $this->issues[] = [
                    'type' => 'Missing Project',
                    'description' => "Agreement ID {$agreement->id} (Student: {$agreement->student->name}) has no associated project",
                    'severity' => 'warning',
                    'entity_type' => 'agreement',
                    'entity_id' => $agreement->id
                ];
            } elseif ($projectRelations->count() > 1) {
                $duplicateProjectAssignments++;
                $this->issues[] = [
                    'type' => 'Duplicate Project Assignment',
                    'description' => "Agreement ID {$agreement->id} is assigned to multiple projects: " . $projectRelations->pluck('id')->join(', '),
                    'severity' => 'error',
                    'entity_type' => 'agreement',
                    'entity_id' => $agreement->id
                ];
            } else {
                $projectsWithAgreements++;
            }
        }

        $this->summary['integrity_issues']['agreements_without_projects'] = $agreementsWithoutProjects;
        $this->summary['integrity_issues']['duplicate_project_assignments'] = $duplicateProjectAssignments;
        $this->summary['integrity_issues']['projects_with_agreements'] = $projectsWithAgreements;

        $this->line("  ✓ Agreements with projects: {$projectsWithAgreements}");
        $this->line("  ⚠ Agreements without projects: {$agreementsWithoutProjects}");
        $this->line("  ❌ Duplicate project assignments: {$duplicateProjectAssignments}");
        $this->newLine();
    }

    /**
     * Check Professor assignment integrity
     */
    protected function checkProfessorAssignmentIntegrity(Year $year): void
    {
        $this->info(__('👨‍🏫 Checking Professor assignment integrity...'));

        $projects = Project::whereHas('final_internship_agreements', function($query) use ($year) {
            $query->where('year_id', $year->id);
        })->with('professors')->get();

        $this->totalProjects = $projects->count();
        $this->summary['total_projects'] = $this->totalProjects;

        $projectsWithoutSupervisor = 0;
        $projectsWithoutReviewers = 0;
        $projectsWithIncompleteJury = 0;
        $duplicateRoleAssignments = 0;
        $invalidProfessorAssignments = 0;

        foreach ($projects as $project) {
            $professors = $project->professors->groupBy('pivot.jury_role');
            
            // Check supervisor assignment
            if (!isset($professors[JuryRole::Supervisor->value]) || $professors[JuryRole::Supervisor->value]->isEmpty()) {
                $projectsWithoutSupervisor++;
                $this->issues[] = [
                    'type' => 'Missing Supervisor',
                    'description' => "Project ID {$project->id} has no supervisor assigned",
                    'severity' => 'error',
                    'entity_type' => 'project',
                    'entity_id' => $project->id
                ];
            } elseif ($professors[JuryRole::Supervisor->value]->count() > 1) {
                $duplicateRoleAssignments++;
                $this->issues[] = [
                    'type' => 'Multiple Supervisors',
                    'description' => "Project ID {$project->id} has multiple supervisors assigned",
                    'severity' => 'error',
                    'entity_type' => 'project',
                    'entity_id' => $project->id
                ];
            }

            // Check reviewer assignments
            $firstReviewer = $professors[JuryRole::FirstReviewer->value] ?? collect();
            $secondReviewer = $professors[JuryRole::SecondReviewer->value] ?? collect();

            if ($firstReviewer->isEmpty() || $secondReviewer->isEmpty()) {
                $projectsWithoutReviewers++;
                $this->issues[] = [
                    'type' => 'Missing Reviewers',
                    'description' => "Project ID {$project->id} has incomplete reviewer assignments (First: {$firstReviewer->count()}, Second: {$secondReviewer->count()})",
                    'severity' => 'warning',
                    'entity_type' => 'project',
                    'entity_id' => $project->id
                ];
            }

            // Check for complete jury
            if ($firstReviewer->isEmpty() || $secondReviewer->isEmpty() || !isset($professors[JuryRole::Supervisor->value])) {
                $projectsWithIncompleteJury++;
            }

            // Validate professor existence and status
            foreach ($project->professors as $professor) {
                if (!$professor->is_enabled || !$professor->can_supervise) {
                    $invalidProfessorAssignments++;
                    $this->issues[] = [
                        'type' => 'Invalid Professor Assignment',
                        'description' => "Project ID {$project->id} has professor {$professor->name} assigned but professor is disabled or cannot supervise",
                        'severity' => 'error',
                        'entity_type' => 'project',
                        'entity_id' => $project->id
                    ];
                }
            }
        }

        $this->summary['integrity_issues']['projects_without_supervisor'] = $projectsWithoutSupervisor;
        $this->summary['integrity_issues']['projects_without_reviewers'] = $projectsWithoutReviewers;
        $this->summary['integrity_issues']['projects_with_incomplete_jury'] = $projectsWithIncompleteJury;
        $this->summary['integrity_issues']['duplicate_role_assignments'] = $duplicateRoleAssignments;
        $this->summary['integrity_issues']['invalid_professor_assignments'] = $invalidProfessorAssignments;

        $this->line("  ✓ Total projects: {$this->totalProjects}");
        $this->line("  ❌ Projects without supervisor: {$projectsWithoutSupervisor}");
        $this->line("  ⚠ Projects without complete reviewers: {$projectsWithoutReviewers}");
        $this->line("  ⚠ Projects with incomplete jury: {$projectsWithIncompleteJury}");
        $this->line("  ❌ Duplicate role assignments: {$duplicateRoleAssignments}");
        $this->line("  ❌ Invalid professor assignments: {$invalidProfessorAssignments}");
        $this->newLine();
    }    /**
     * Check Department distribution integrity
     */
    protected function checkDepartmentDistributionIntegrity(Year $year): void
    {
        $this->info(__('🏢 Checking Department distribution integrity...'));

        $departmentFilter = $this->option('department');
        if ($departmentFilter) {
            $department = Department::from($departmentFilter);
            $this->checkDepartmentStatistics($department);
        } else {
            $departments = Department::cases();
            foreach ($departments as $department) {
                $this->checkDepartmentStatistics($department);
            }
        }
    }

    /**
     * Check statistics for a specific department using the statistics service
     */
    protected function checkDepartmentStatistics(Department $department): void
    {
        $stats = $this->statisticsService->getDepartmentStatistics($department)->first();
        
        $professorCount = $stats['professors_count'];
        $this->totalProfessors += $professorCount;

        $projectsByAssignedDept = $stats['projects_by_assigned_department'];
        $projectsByProfessorDept = $stats['projects_by_professor_department'];
        $mentoringStats = $stats['mentoring_stats'];

        $this->line("📋 Department: {$department->getLabel()} ({$department->value})");
        $this->line("   Professors: {$professorCount}");
        $this->line("   Projects (by assigned dept): {$projectsByAssignedDept}");
        $this->line("   Projects (by professor dept): {$projectsByProfessorDept}");
        $this->line("   Avg Supervising: {$mentoringStats['avg_supervising']}");
        $this->line("   Avg Reviewing: {$mentoringStats['avg_reviewing']}");
        $this->line("   Total Avg: {$mentoringStats['total_avg']}");
        $this->newLine();

        // Check for mismatches
        if ($projectsByAssignedDept !== $projectsByProfessorDept) {
            $this->issues[] = [
                'type' => 'Department Assignment Mismatch',
                'description' => "Department {$department->value}: Projects by assigned_department ({$projectsByAssignedDept}) != Projects by professor department ({$projectsByProfessorDept})",
                'severity' => 'warning',                'entity_type' => 'department',
                'entity_id' => $department->value
            ];
        }
    }    /**
     * Check Mentoring statistics integrity using the statistics service
     */
    protected function checkMentoringStatisticsIntegrity(Year $year): void    {
        $this->info(__('📊 Checking Mentoring statistics integrity...'));
        
        // Ensure the service is using the correct year
        $this->statisticsService = new ProjectStatisticsService($year);
        $mentoringStats = $this->statisticsService->getMentoringStatisticsSorted();
        
        $this->line('  📈 Department Mentoring Statistics (sorted by total average):');
        foreach ($mentoringStats as $stats) {
            $this->line("    {$stats['department_name']}: {$stats['professors_count']} professors, " .
                       "Avg Supervising: {$stats['avg_supervising']}, " .
                       "Avg Reviewing: {$stats['avg_reviewing']}, " .
                       "Total Avg: {$stats['total_avg']}");
        }
        
        $this->newLine();

        // Store mentoring stats in summary
        $this->summary['mentoring_statistics'] = $mentoringStats->toArray();
    }

    /**
     * Calculate supervision statistics for professors
     */
    protected function calculateSupervisionStatistics(Collection $professors, Year $year): array
    {
        $supervisionCounts = [];
        $reviewingCounts = [];

        foreach ($professors as $professor) {
            // Count supervisions
            $supervisions = $professor->projects()
                ->wherePivot('jury_role', JuryRole::Supervisor->value)
                ->whereHas('final_internship_agreements', function($query) use ($year) {
                    $query->where('year_id', $year->id);
                })
                ->count();

            // Count reviews
            $reviews = $professor->projects()
                ->whereIn('professor_projects.jury_role', [JuryRole::FirstReviewer->value, JuryRole::SecondReviewer->value])
                ->whereHas('final_internship_agreements', function($query) use ($year) {
                    $query->where('year_id', $year->id);
                })
                ->count();

            $supervisionCounts[] = $supervisions;
            $reviewingCounts[] = $reviews;
        }

        return [
            'avg_supervising' => $professors->count() > 0 ? round(array_sum($supervisionCounts) / $professors->count(), 2) : 0,
            'avg_reviewing' => $professors->count() > 0 ? round(array_sum($reviewingCounts) / $professors->count(), 2) : 0,
            'total_avg' => $professors->count() > 0 ? round((array_sum($supervisionCounts) + array_sum($reviewingCounts)) / $professors->count(), 2) : 0,
            'total_supervisions' => array_sum($supervisionCounts),
            'total_reviews' => array_sum($reviewingCounts)
        ];
    }

    /**
     * Check for orphaned relationships
     */
    protected function checkOrphanedRelationships(Year $year): void
    {
        $this->info(__('🔍 Checking for orphaned relationships...'));

        // Check for projects without agreements
        $projectsWithoutAgreements = Project::whereDoesntHave('final_internship_agreements')->count();
        
        // Check for professor_projects with invalid professor_id
        $invalidProfessorProjects = \DB::table('professor_projects')
            ->leftJoin('users', 'professor_projects.professor_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();

        // Check for professor_projects with invalid project_id
        $invalidProjectProfessors = \DB::table('professor_projects')
            ->leftJoin('projects', 'professor_projects.project_id', '=', 'projects.id')
            ->whereNull('projects.id')
            ->count();

        if ($projectsWithoutAgreements > 0) {
            $this->issues[] = [
                'type' => 'Orphaned Projects',
                'description' => "{$projectsWithoutAgreements} projects exist without any internship agreements",
                'severity' => 'warning',
                'entity_type' => 'project',
                'entity_id' => null
            ];
        }

        if ($invalidProfessorProjects > 0) {
            $this->issues[] = [
                'type' => 'Invalid Professor References',
                'description' => "{$invalidProfessorProjects} professor_project records reference non-existent professors",
                'severity' => 'error',
                'entity_type' => 'professor_project',
                'entity_id' => null
            ];
        }

        if ($invalidProjectProfessors > 0) {
            $this->issues[] = [
                'type' => 'Invalid Project References',
                'description' => "{$invalidProjectProfessors} professor_project records reference non-existent projects",
                'severity' => 'error',
                'entity_type' => 'professor_project',
                'entity_id' => null
            ];
        }

        $this->line("  ✓ Projects without agreements: {$projectsWithoutAgreements}");
        $this->line("  ✓ Invalid professor references: {$invalidProfessorProjects}");
        $this->line("  ✓ Invalid project references: {$invalidProjectProfessors}");
        $this->newLine();
    }

    /**
     * Check general data consistency
     */
    protected function checkDataConsistency(Year $year): void
    {
        $this->info(__('🔄 Checking data consistency...'));

        // Check for professors without department
        $professorsWithoutDepartment = Professor::whereNull('department')->count();
        
        // Check for agreements without assigned department
        $agreementsWithoutDepartment = FinalYearInternshipAgreement::where('year_id', $year->id)
            ->whereNull('assigned_department')->count();

        // Check for projects with mismatched timelines
        $projectsWithInvalidDates = Project::whereHas('final_internship_agreements', function($query) use ($year) {
            $query->where('year_id', $year->id);
        })->where(function($query) {
            $query->whereNull('start_date')
                  ->orWhereNull('end_date')
                  ->orWhereRaw('start_date > end_date');
        })->count();

        if ($professorsWithoutDepartment > 0) {
            $this->issues[] = [
                'type' => 'Professors Without Department',
                'description' => "{$professorsWithoutDepartment} professors have no department assigned",
                'severity' => 'warning',
                'entity_type' => 'professor',
                'entity_id' => null
            ];
        }

        if ($agreementsWithoutDepartment > 0) {
            $this->issues[] = [
                'type' => 'Agreements Without Department',
                'description' => "{$agreementsWithoutDepartment} agreements have no assigned department",
                'severity' => 'error',
                'entity_type' => 'agreement',
                'entity_id' => null
            ];
        }

        if ($projectsWithInvalidDates > 0) {
            $this->issues[] = [
                'type' => 'Projects With Invalid Dates',
                'description' => "{$projectsWithInvalidDates} projects have invalid or missing start/end dates",
                'severity' => 'warning',
                'entity_type' => 'project',
                'entity_id' => null
            ];
        }

        $this->line("  ✓ Professors without department: {$professorsWithoutDepartment}");
        $this->line("  ✓ Agreements without department: {$agreementsWithoutDepartment}");
        $this->line("  ✓ Projects with invalid dates: {$projectsWithInvalidDates}");
        $this->newLine();
    }

    /**
     * Display detailed department information
     */
    protected function displayDepartmentDetails(array $departmentData): void
    {
        $this->info("📋 Department: {$departmentData['name']} ({$departmentData['code']})");
        $this->line("   Professors: {$departmentData['professor_count']}");
        $this->line("   Projects (by assigned dept): {$departmentData['projects_by_assigned_dept']}");
        $this->line("   Projects (by professor dept): {$departmentData['projects_by_professor_dept']}");
        $this->line("   Avg Supervising: {$departmentData['supervision_stats']['avg_supervising']}");
        $this->line("   Avg Reviewing: {$departmentData['supervision_stats']['avg_reviewing']}");
        $this->line("   Total Avg: {$departmentData['supervision_stats']['total_avg']}");
        $this->newLine();
    }

    /**
     * Display final results
     */
    protected function displayResults(): void
    {
        $this->info(__('📊 INTEGRITY CHECK SUMMARY'));
        $this->line('=' . str_repeat('=', 50));
          // Summary statistics
        $this->line(__('Year') . ": {$this->summary['year']}");
        $this->line(__('Total Projects') . ": {$this->summary['total_projects']}");
        $this->line(__('Total Professors') . ": {$this->totalProfessors}");
        $this->line(__('Total Agreements') . ": {$this->summary['total_agreements']}");
        $this->newLine();

        // Issues by severity
        $errors = array_filter($this->issues, fn($issue) => $issue['severity'] === 'error');
        $warnings = array_filter($this->issues, fn($issue) => $issue['severity'] === 'warning');

        if (!empty($errors)) {
            $this->error("❌ " . __('ERRORS') . " (" . count($errors) . "):");
            foreach ($errors as $error) {
                $this->line("  • [{$error['type']}] {$error['description']}");
            }
            $this->newLine();
        }

        if (!empty($warnings)) {
            $this->warn("⚠️  " . __('WARNINGS') . " (" . count($warnings) . "):");
            $this->warn("⚠ WARNINGS (" . count($warnings) . "):");
            foreach ($warnings as $warning) {
                $this->line("  • [{$warning['type']}] {$warning['description']}");
            }
            $this->newLine();
        }        // Display mentoring statistics summary
        if (!empty($this->summary['mentoring_statistics'])) {
            $this->info(__('📈 MENTORING STATISTICS SUMMARY:'));
            foreach ($this->summary['mentoring_statistics'] as $stat) {
                $this->line("{$stat['department_name']}: {$stat['professors_count']} professors, Avg Supervising: {$stat['avg_supervising']}, Avg Reviewing: {$stat['avg_reviewing']}, Total Avg: {$stat['total_avg']}");
            }
        }
    }

    /**
     * Export results to file
     */
    protected function exportResults(): void
    {
        $format = $this->option('export');
        $filename = 'project_statistics_integrity_' . date('Y-m-d_H-i-s') . '.' . $format;
        $filepath = storage_path('app/reports/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $data = [
            'summary' => $this->summary,
            'issues' => $this->issues,
            'generated_at' => now()->toISOString()
        ];

        if ($format === 'json') {
            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
        } elseif ($format === 'csv') {
            $this->exportToCsv($filepath, $data);
        }

        $this->info("📄 Results exported to: {$filepath}");
    }

    /**
     * Export data to CSV format
     */
    protected function exportToCsv(string $filepath, array $data): void
    {
        $handle = fopen($filepath, 'w');
        
        // Write issues
        fputcsv($handle, ['Type', 'Description', 'Severity', 'Entity Type', 'Entity ID']);
        foreach ($this->issues as $issue) {
            fputcsv($handle, [
                $issue['type'],
                $issue['description'],
                $issue['severity'],
                $issue['entity_type'],
                $issue['entity_id'] ?? 'N/A'
            ]);
        }

        fclose($handle);
    }

    /**
     * Attempt to fix orphaned relationships
     */
    protected function fixOrphanedRelationships(): void
    {
        $this->info(__('🔧 Attempting to fix orphaned relationships...'));

        $fixed = 0;

        // Clean up invalid professor_project references
        $deletedProfessorProjects = \DB::table('professor_projects')
            ->leftJoin('users', 'professor_projects.professor_id', '=', 'users.id')
            ->whereNull('users.id')
            ->delete();

        $deletedProjectProfessors = \DB::table('professor_projects')
            ->leftJoin('projects', 'professor_projects.project_id', '=', 'projects.id')
            ->whereNull('projects.id')
            ->delete();

        $fixed += $deletedProfessorProjects + $deletedProjectProfessors;

        $this->info("🔧 Fixed {$fixed} orphaned relationships");
        
        if ($fixed > 0) {
            $this->info("  • Deleted {$deletedProfessorProjects} invalid professor references");
            $this->info("  • Deleted {$deletedProjectProfessors} invalid project references");
        }
    }
}
