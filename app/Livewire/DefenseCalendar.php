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
    public $data;
    public $nonPlannedProjects;
    public $islamicHoliday;    public function mount()
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
    }    private function applySearchToQuery($query)
    {
        if (empty($this->search)) {
            return $query;
        }

        $searchTerm = '%' . $this->search . '%';

        return $query->whereHas('project', function($projectQuery) use ($searchTerm) {
            $projectQuery->where(function($q) use ($searchTerm) {
                // Student or PFE ID search
                if ($this->searchField === 'student' || $this->searchField === 'pfe_id' || $this->searchField === 'all') {
                    $q->orWhereHas('agreements', function($agreementQuery) use ($searchTerm) {
                        $agreementQuery->whereHasMorph(
                            'agreeable',
                            [FinalYearInternshipAgreement::class],
                            function($morphQuery) use ($searchTerm) {
                                $morphQuery->whereHas('student', function($studentQuery) use ($searchTerm) {
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
                            }
                        );
                    });
                }

                // Professor search
                if ($this->searchField === 'professor' || $this->searchField === 'all') {
                    $q->orWhereHas('professors', function($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    });
                }

                // Organization search
                if ($this->searchField === 'organization' || $this->searchField === 'all') {
                    $q->orWhereHas('organization', function($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    });
                }
            });
        });
    }

    private function getIslamicHoliday($date)
    {
        // Convert date to Y-m-d format for comparison
        $dateStr = $date->format('Y-m-d');
        
        // Define the specific date for 1st Muharram 1447
        if ($dateStr === '2025-06-27') {
            return [
                'is_holiday' => true,
                'type' => 'holiday',
                'date' => $dateStr,
                'message' => 'فاتح شهر محرم 1447',
                'gregorian_date' => 'Vendredi 27 Juin 2025',
                'id' => 'holiday-' . $dateStr,
            ];
        }
        
        return [
            'is_holiday' => false,
            'type' => 'regular',
            'date' => $dateStr,
            'message' => '',
            'gregorian_date' => '',
            'id' => 'regular-' . $dateStr,
        ];
    }

    private function loadData()
    {
        try {
            \Log::info('Starting to load defense data');

            // Check today's date for Islamic holiday
            $today = now();
            $this->islamicHoliday = $this->getIslamicHoliday($today);

            // Get current academic year
            $currentYear = \App\Models\Year::current();
            \Log::info('Current academic year ID: ' . $currentYear->id);

            // Start with Timetables and their relationships
            $query = Timetable::query()
                ->with([
                    'timeslot',
                    'room',
                    'project' => function($query) {
                        $query->with([
                            'agreements.agreeable.student',
                            'professors',
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
                ->whereHas('project');

            // Apply search if needed
            if (!empty($this->search)) {
                $query = $this->applySearchToQuery($query);
            }            // Apply ordering by date and time
            $query->join('timeslots', 'timetables.timeslot_id', '=', 'timeslots.id')
                  ->where('timeslots.is_enabled', true)
                  ->orderBy('timeslots.start_time', 'asc')
                  ->select('timetables.*');

            $timetables = $query->get();
            
            \Log::info('Found timetables for current year: ' . $timetables->count());

            $processedDates = collect();
            $this->data = collect();

            // Process timetables
            $timetables->each(function($timetable) use (&$processedDates) {
                try {
                    $startTime = \Carbon\Carbon::parse($timetable->timeslot->start_time);
                    $dateStr = $startTime->format('Y-m-d');

                    // Check for Islamic holiday
                    $holiday = $this->getIslamicHoliday($startTime);
                    if ($holiday['type'] === 'holiday' && !$processedDates->contains($dateStr)) {
                        $processedDates->push($dateStr);
                        $this->data->push($holiday);
                    }

                    $project = $timetable->project;
                    if (!$project || !$timetable->timeslot) {
                        return;
                    }

                    // Get all students from agreements
                    $students = collect();
                    foreach ($project->agreements as $agreement) {
                        if ($agreement->agreeable && method_exists($agreement->agreeable, 'student')) {
                            $student = $agreement->agreeable->student;
                            if ($student) {
                                $students->push([                                    'name' => $student->first_name . ' ' . $student->last_name,
                                    'id_pfe' => $student->id_pfe,
                                    'program' => $student->program,
                                    'exchange_partner' => $student->exchangePartner?->name
                                ]);
                            }
                        }
                    }
                    
                    // Get professors with their roles
                    $supervisor = $project->academic_supervisor();
                    $reviewers = $project->reviewers()->get();

                    // Format jury with role distinctions
                    $juryDisplay = [];
                    if ($supervisor) {
                        $juryDisplay[] = "Encadrant: " . $supervisor->name;
                    }
                    if ($reviewers->isNotEmpty()) {
                        $juryDisplay[] = "Examinateurs: " . $reviewers->pluck('name')->implode(', ');
                    }
                    
                    $authorization = $this->getAuthorizationStatus($project);
                    
                    $this->data->push([
                        'id' => $timetable->id,
                        'type' => 'defense',
                        'date' => $dateStr,
                        'project_id' => $project->id,
                        'Date Soutenance' => $startTime->format('d/m/Y'),
                        'Heure' => $startTime->format('H:i') . ' - ' . \Carbon\Carbon::parse($timetable->timeslot->end_time)->format('H:i'),
                        'Lieu' => $timetable->room?->name ?? 'Non définie',
                        'Students' => $students->toArray(),
                        'Organisation' => $project->organization?->name ?? 'Non définie',
                        'Jury' => implode("\n", $juryDisplay) ?: 'Non assigné',
                        'Autorisation' => $authorization,
                    ]);

                } catch (\Exception $e) {
                    \Log::error('Error processing timetable: ' . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                }
            });

            // Sort the final collection by date
            $this->data = $this->data->sortBy(function ($item) {
                return $item['date'];
            })->values();

        } catch (\Exception $e) {
            \Log::error('Error in loadData: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
        }
    }

    private function loadUnplannedProjects()
    {
        $currentYear = \App\Models\Year::current();
        
        $query = Project::query()
            ->whereDoesntHave('timetable')
            ->whereHas('final_internship_agreements', function($query) use ($currentYear) {
                $query->whereHas('student', function($query) use ($currentYear) {
                    $query->where('year_id', $currentYear->id);
                });
            });

        // Apply search if needed
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            
            if ($this->searchField === 'student') {
                $query->whereHas('final_internship_agreements.student', function($query) use ($searchTerm) {
                    $query->where('first_name', 'like', $searchTerm)
                          ->orWhere('last_name', 'like', $searchTerm)
                          ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $searchTerm);
                });
            }
            elseif ($this->searchField === 'pfe_id') {
                $query->whereHas('final_internship_agreements.student', function($query) use ($searchTerm) {
                    $query->where('id_pfe', 'like', $searchTerm);
                });
            }
            elseif ($this->searchField === 'professor') {
                $query->whereHas('professors', function($query) use ($searchTerm) {
                    $query->where('name', 'like', $searchTerm);
                });
            }
            elseif ($this->searchField === 'organization') {
                $query->whereHas('organization', function($query) use ($searchTerm) {
                    $query->where('name', 'like', $searchTerm);
                });
            }
            elseif ($this->searchField === 'all') {
                $query->where(function($query) use ($searchTerm) {
                    $query->whereHas('final_internship_agreements.student', function($query) use ($searchTerm) {
                        $query->where('first_name', 'like', $searchTerm)
                              ->orWhere('last_name', 'like', $searchTerm)
                              ->orWhere('id_pfe', 'like', $searchTerm)
                              ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $searchTerm);
                    })
                    ->orWhereHas('professors', function($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('organization', function($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm);
                    });
                });
            }
        }

        $projects = $query->with([
            'final_internship_agreements.student',
            'professors' => function($query) {
                $query->withPivot('jury_role');
            },
            'organization'
        ])->get();

        $this->nonPlannedProjects = $projects->map(function($project) {
            $students = collect();
            foreach ($project->final_internship_agreements as $agreement) {
                $student = $agreement->student;
                if ($student) {
                    $students->push([
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'id_pfe' => $student->id_pfe,
                        'program' => $student->program,
                        'exchange_partner' => $student->exchangePartner?->name
                    ]);
                }
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
        });
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
        // Always check today for Islamic holiday status
        $today = now();
        $this->islamicHoliday = $this->getIslamicHoliday($today);

        return view('livewire.defense-calendar');
    }
}
