<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Timetable;
use App\Models\FinalYearInternshipAgreement;
use Illuminate\Support\Collection;
use App\Enums;
class DefenseCalendar extends Component
{
    public $search = '';
    public $searchField = 'all';
    public $programFilter = '';
    public $data;
    public $nonPlannedProjects;
    public $islamicHoliday;

    public function mount()
    {
        $this->data = collect([]);
        $this->nonPlannedProjects = collect([]);
        $this->islamicHoliday = [
            'is_holiday' => false,
            'message' => '',
            'gregorian_date' => ''
        ];
        $this->loadData();
        $this->loadUnplannedProjects();
    }    public function updatedSearch()
    {
        $this->loadData();
        $this->loadUnplannedProjects();
    }

    public function updatedSearchField()
    {
        $this->loadData();
        $this->loadUnplannedProjects();
    }

    public function updatedProgramFilter()
    {
        $this->loadData();
        $this->loadUnplannedProjects();
    }    private function applySearchToQuery($query)
    {
        if (empty($this->search) && empty($this->programFilter)) {
            return $query;
        }

        $searchTerm = '%' . $this->search . '%';

        $query->where(function($mainQuery) use ($searchTerm) {
            // Apply text search if there is a search term
            if (!empty($this->search)) {
                $mainQuery->where(function($q) use ($searchTerm) {
                    // Student or PFE ID search
                    if ($this->searchField === 'student' || $this->searchField === 'pfe_id' || $this->searchField === 'all') {
                        $q->orWhereHas('project.final_internship_agreements', function($agreementQuery) use ($searchTerm) {
                            $agreementQuery->whereHas('student', function($studentQuery) use ($searchTerm) {
                                $studentQuery->where(function($sq) use ($searchTerm) {
                                    $sq->where('first_name', 'like', $searchTerm)
                                      ->orWhere('last_name', 'like', $searchTerm)
                                      ->orWhere('id_pfe', 'like', $searchTerm)
                                      ->orWhere(
                                          \DB::raw("CONCAT(first_name, ' ', last_name)"),
                                          'like',
                                          $searchTerm
                                      );
                                });
                            });
                        });
                    }

                    // Professor search
                    if ($this->searchField === 'professor' || $this->searchField === 'all') {
                        $q->orWhereHas('project.professors', function($sq) use ($searchTerm) {
                            $sq->where('name', 'like', $searchTerm);
                        });
                    }

                    // Organization search
                    if ($this->searchField === 'organization' || $this->searchField === 'all') {
                        $q->orWhereHas('project.organization', function($sq) use ($searchTerm) {
                            $sq->where('name', 'like', $searchTerm);
                        });
                    }
                });
            }

            // Apply program filter if selected
            if (!empty($this->programFilter)) {
                $mainQuery->whereHas('project.final_internship_agreements.student', function($query) {
                    $query->where('program', \Str::upper($this->programFilter));
                });
            }
        });

        return $query;
    }

    private function applyProgramFilterToQuery($query)
    {
        if (empty($this->programFilter)) {
            return $query;
        }

        return $query->whereHas('final_internship_agreements.student', function($query) {
            $query->where('program', \Str::upper($this->programFilter));
        });
    }

    private function getIslamicHoliday($date)
    {
        // Convert date to Y-m-d format for comparison
        $dateStr = $date->format('Y-m-d');
        
        // Define the specific date for 1st Muharram 1447
        if ($dateStr === '2025-06-27') {
            $formattedDate = $date->translatedFormat('l j F Y');
            return [
                'id' => 'holiday-' . $dateStr,
                'is_holiday' => true,
                'type' => 'holiday',
                'date' => $dateStr,
                'islamic_date' => 'فاتح شهر محرم 1447',
                'gregorian_date' => $formattedDate,
                'message' => 'فاتح شهر محرم 1447 - Vendredi 27 Juin 2025',
            ];
        }
        
        return [
            'id' => 'regular-' . $dateStr,
            'is_holiday' => false,
            'type' => 'regular',
            'date' => $dateStr,
            'islamic_date' => '',
            'gregorian_date' => '',
            'message' => '',
        ];
    }

    private function loadData()
    {
        try {
            \Log::info('Starting to load defense data');
            
            // Initialize collections
            $this->data = collect();

            // Get current academic year
            $currentYear = \App\Models\Year::current();
            \Log::info('Current academic year ID: ' . $currentYear->id);

            // Get timetables with their relationships
            $query = Timetable::query()
                ->with([
                    'timeslot',
                    'room',
                    'project' => function($query) {
                        $query->withoutTrashed()->with([
                            'final_internship_agreements.student',
                            'professors' => function($query) {
                                $query->withPivot('jury_role');
                            },
                            'organization'
                        ]);
                    }
                ])
                ->whereHas('timeslot', function($q) use ($currentYear) {
                    $q->whereNotNull('start_time')
                      ->whereNotNull('end_time')
                      ->where('is_enabled', true)
                      ->where('year_id', $currentYear->id);
                })
                ->whereHas('project', function($q) {
                    $q->withoutTrashed()->whereHas('final_internship_agreements', function($q) {
                        $q->whereHas('student', function($q) {
                            $q->whereNull('deleted_at');
                            
                            // Apply program filter if selected
                            if (!empty($this->programFilter)) {
                                $q->where('program', \Str::upper($this->programFilter));
                            }
                        });
                    });
                });

            // Apply search if needed
            if (!empty($this->search)) {
                $query = $this->applySearchToQuery($query);
            }

            // Apply ordering by date and time
            $query->join('timeslots', 'timetables.timeslot_id', '=', 'timeslots.id')
                  ->where('timeslots.is_enabled', true)
                  ->orderBy('timeslots.start_time', 'asc')
                  ->select('timetables.*');

            $timetables = $query->get();
            
            \Log::info('Found timetables for current year: ' . $timetables->count());

            // Process timetables and build data collection
            $defenseData = collect();
            $hasDefenseOnHoliday = false;

            // Process each timetable
            $timetables->each(function($timetable) use (&$defenseData, &$hasDefenseOnHoliday) {
                // Process defense data as before
                if (!$timetable->project || !$timetable->timeslot) {
                    return;
                }

                $startTime = \Carbon\Carbon::parse($timetable->timeslot->start_time);
                $dateStr = $startTime->format('Y-m-d');

                // Check if this defense is on the holiday
                if ($dateStr === '2025-06-27') {
                    $hasDefenseOnHoliday = true;
                }

                // Get all students from agreements and find admin supervisor
                $students = collect();
                $adminSupervisor = null;
                foreach ($timetable->project->final_internship_agreements as $agreement) {
                    $student = $agreement->student;
                    if ($student && is_null($student->deleted_at)) {
                        $students->push([
                            'name' => $student->first_name . ' ' . $student->last_name,
                            'id_pfe' => $student->id_pfe,
                            'program' => $student->program,
                            'exchange_partner' => $student->exchangePartner?->name
                        ]);
                        
                        // Get admin supervisor from first active student
                        if (!$adminSupervisor && $student->administrative_supervisor) {
                            $adminSupervisor = $student->administrative_supervisor;
                        }
                    }
                }
                
                if ($students->isEmpty()) {
                    return;
                }

                // Get professors with their roles
                $supervisor = $timetable->project->professors
                    ->where('pivot.jury_role', \App\Enums\JuryRole::Supervisor->value)
                    ->first();

                $firstReviewer = $timetable->project->professors
                    ->where('pivot.jury_role', \App\Enums\JuryRole::FirstReviewer->value)
                    ->first();

                $secondReviewer = $timetable->project->professors
                    ->where('pivot.jury_role', \App\Enums\JuryRole::SecondReviewer->value)
                    ->first();

                // Format jury display
                $juryDisplay = [];
                if ($supervisor) {
                    $juryDisplay[] = "Encadrant: " . $supervisor->name;
                }

                $reviewerNames = collect([$firstReviewer, $secondReviewer])
                    ->filter()
                    ->pluck('name');

                if ($reviewerNames->isNotEmpty()) {
                    $juryDisplay[] = "Examinateurs: " . $reviewerNames->implode(', ');
                }

                $defenseData->push([
                    'id' => $timetable->id,
                    'type' => 'defense',
                    'date' => $dateStr,
                    'project_id' => $timetable->project->id,
                    'Date Soutenance' => $startTime->format('d/m/Y'),
                    'Heure' => $startTime->format('H:i') . ' - ' . \Carbon\Carbon::parse($timetable->timeslot->end_time)->format('H:i'),
                    'Lieu' => $timetable->room?->name ?? 'Non définie',
                    'Students' => $students->toArray(),
                    'Organisation' => $timetable->project->organization?->name ?? 'Non définie',
                    'Jury' => implode("\n", $juryDisplay) ?: 'Non assigné',
                    'Autorisation' => $this->getAuthorizationStatus($timetable->project),
                    'AdminSupervisor' => $adminSupervisor?->name ?? null
                ]);
            });

            // Check if we need to add the holiday entry
            if (!$hasDefenseOnHoliday) {
                // Add the holiday entry for June 27, 2025
                $holiday = $this->getIslamicHoliday(\Carbon\Carbon::parse('2025-06-27'));
                if ($holiday['is_holiday']) {                    $defenseData->push([
                        'id' => 'holiday-2025-06-27',
                        'type' => 'holiday',
                        'date' => '2025-06-27',
                        'islamic_date' => 'فاتح شهر محرم 1447',
                        'gregorian_date' => 'Vendredi 27 juin 2025'
                    ]);
                }
            }

            // Sort the final collection by date
            $this->data = $defenseData->sortBy('date')->values();

        } catch (\Exception $e) {
            \Log::error('Error in loadData: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
        }
    }

    private function loadUnplannedProjects()
    {
        $currentYear = \App\Models\Year::current();
        
        // Start with all valid projects in current year
        $query = Project::query()
            ->withoutTrashed()  // Exclude soft-deleted projects
            ->whereHas('final_internship_agreements', function($query) use ($currentYear) {
                $query->whereHas('student', function($query) use ($currentYear) {
                    $query->whereNull('deleted_at')  // Only active students
                          ->where('year_id', $currentYear->id);
                });
            });            // Apply search and program filters
            $query->where(function($mainQuery) {
                // Apply text search if there is a search term
                if (!empty($this->search)) {
                    $searchTerm = '%' . $this->search . '%';
                    
                    $mainQuery->where(function($query) use ($searchTerm) {
                        if ($this->searchField === 'student' || $this->searchField === 'all') {
                            $query->orWhereHas('final_internship_agreements.student', function($query) use ($searchTerm) {
                                $query->whereNull('deleted_at')
                                      ->where(function($q) use ($searchTerm) {
                                          $q->where('first_name', 'like', $searchTerm)
                                            ->orWhere('last_name', 'like', $searchTerm)
                                            ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $searchTerm);
                                      });
                            });
                        }
                        
                        if ($this->searchField === 'pfe_id' || $this->searchField === 'all') {
                            $query->orWhereHas('final_internship_agreements.student', function($query) use ($searchTerm) {
                                $query->whereNull('deleted_at')
                                      ->where('id_pfe', 'like', $searchTerm);
                            });
                        }
                        
                        if ($this->searchField === 'professor' || $this->searchField === 'all') {
                            $query->orWhereHas('professors', function($query) use ($searchTerm) {
                                $query->where('name', 'like', $searchTerm);
                            });
                        }
                        
                        if ($this->searchField === 'organization' || $this->searchField === 'all') {
                            $query->orWhereHas('organization', function($query) use ($searchTerm) {
                                $query->where('name', 'like', $searchTerm);
                            });
                        }
                    });
                }

                // Apply program filter if selected
                if (!empty($this->programFilter)) {
                    $mainQuery->whereHas('final_internship_agreements.student', function($query) {
                        $query->where('program', \Str::upper($this->programFilter));
                    });
                }
            });

        // Eager load relationships
        $projects = $query->with([
            'final_internship_agreements.student.exchangePartner',
            'professors' => function($query) {
                $query->withPivot('jury_role');
            },
            'organization',
            'timetable.timeslot'
        ])
        ->get()
        ->filter(function($project) {
            // Consider a project as unplanned if:
            // 1. It has no timetable OR
            // 2. Its timetable has no timeslot OR
            // 3. Its timetable's timeslot is not enabled
            return !$project->timetable || 
                   !$project->timetable->timeslot ||
                   !$project->timetable->timeslot->is_enabled;
        });

        $this->nonPlannedProjects = $projects->map(function($project) {
            $students = collect();
            foreach ($project->final_internship_agreements as $agreement) {
                $student = $agreement->student;
                if ($student && is_null($student->deleted_at)) {
                    $students->push([
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'id_pfe' => $student->id_pfe,
                        'program' => $student->program,
                        'exchange_partner' => $student->exchangePartner?->name
                    ]);
                }
            }

            // Skip if no valid students found
            if ($students->isEmpty()) {
                return null;
            }

            $supervisor = $project->professors
                ->where('pivot.jury_role', \App\Enums\JuryRole::Supervisor->value)
                ->first();

            $firstReviewer = $project->professors
                ->where('pivot.jury_role', \App\Enums\JuryRole::FirstReviewer->value)
                ->first();

            $secondReviewer = $project->professors
                ->where('pivot.jury_role', \App\Enums\JuryRole::SecondReviewer->value)
                ->first();

            return [
                'id' => $project->id,
                'students' => $students,
                'supervisor' => $supervisor?->name ?? 'Non assigné',
                'first_reviewer' => $firstReviewer?->name ?? 'Non assigné',
                'second_reviewer' => $secondReviewer?->name ?? 'Non assigné',
                'organisation' => $project->organization?->name ?? 'Non définie'
            ];
        })->filter();
    }

    private function getStatusInFrench($status)
    {
        return match($status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => 'En attente'
        };
    }

    private function getAuthorizationStatus($project)
    {
        if (!$project) {
            return [
                'status' => 'warning',
                'message' => 'En attente'
            ];
        }

        try {
            switch ($project->defense_status) {
                case Enums\DefenseStatus::Authorized:
                    return [
                        'status' => 'success',
                        'message' => 'Autorisée'
                    ];
                case Enums\DefenseStatus::Completed:
                    return [
                        'status' => 'success',
                        'message' => 'Complétée'
                    ];
                case Enums\DefenseStatus::Postponed:
                    return [
                        'status' => 'warning',
                        'message' => 'Reportée'
                    ];
                case Enums\DefenseStatus::Rejected:
                    return [
                        'status' => 'danger',
                        'message' => 'Rejetée'
                    ];
                default:
                    return [
                        'status' => 'warning',
                        'message' => 'En attente'
                    ];
            }
        } catch (\Exception $e) {
            \Log::error('Error getting authorization status: ' . $e->getMessage());
            return [
                'status' => 'warning',
                'message' => 'En attente'
            ];
        }
    }

    public function render()
    {
        // Ensure the holiday state is always up to date
        $holidayDate = \Carbon\Carbon::parse('2025-06-27');
        $this->islamicHoliday = $this->getIslamicHoliday($holidayDate);

        return view('livewire.defense-calendar');
    }
}
