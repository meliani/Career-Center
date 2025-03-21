<?php

namespace App\Services;

use App\Enums;
use App\Facades\GlobalDefenseCalendarConnector;
use App\Models\InternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Filament;
use Filament\Notifications\Notification;

class GoogleServices
{
    protected $connector;

    protected $data;

    protected $unfoundProfessors = [];

    protected $anomalies = [];

    public function __construct()
    {
        $this->connector = new GlobalDefenseCalendarConnector;
        $this->data = $this->connector->getDefenses();

    }

    public function getIntenshipAgreement($pfeId)
    {
        $internshipAgreement = InternshipAgreement::where('id_pfe', $pfeId)->first();
        if ($internshipAgreement) {
            return $internshipAgreement;
        } else {
            $pfeIds = explode(',', $pfeId);
            foreach ($pfeIds as $id) {
                $internshipAgreement = InternshipAgreement::where('id_pfe', $id)->first();
                if ($internshipAgreement) {
                    return $internshipAgreement;
                }
            }
        }
    }

    public function checkChangedProfessors()
    {
        // here we want to check if the professors are changed in the google sheet for Authorized and Completed projects
        // we will get the professors from the google sheet and compare them with the professors in the database
        // if they are different we will will display a list at the end of the comparison

        $changedProfessors = [];
        foreach ($this->data as $record) {
            $internshipAgreement = $this->getIntenshipAgreement($record['ID PFE']);
            if ($internshipAgreement) {
                $project = $internshipAgreement->project;
                if ($project->defense_status === Enums\DefenseStatus::Authorized || $project->defense_status === Enums\DefenseStatus::Completed) {
                    // lets get the professors with pivot jury_role : supervisor, reviewer1, reviewer2

                    $professors = $project->professors->map(function ($professor) {
                        return [
                            'name' => $professor->name,
                            'role' => $professor->pivot->jury_role->value,
                        ];
                    });

                    $professorsFromSheet = [
                        'Supervisor' => $record['Encadrant Interne'],
                        'Reviewer1' => $record['Nom et Prénom Examinateur 1'],
                        'Reviewer2' => $record['Nom et Prénom Examinateur 2'],
                    ];

                    foreach ($professors as $professor) {
                        if ($professor['name'] !== $professorsFromSheet[$professor['role']]) {
                            $changedProfessors[] = __('Project :project_id has changed professor :professor_role from :old_professor to :new_professor', ['project_id' => $project->id_pfe, 'professor_role' => $professor['role'], 'old_professor' => $professor['name'], 'new_professor' => $professorsFromSheet[$professor['role']]]);
                        }
                    }

                }
            }
        }

        if (! empty($changedProfessors)) {
            $message = implode("\n", $changedProfessors); // Concatenate all messages, or format them as you prefer
            dd($message);
        }

    }

    public function importData()
    {
        $this->importProfessors();

        foreach ($this->data as $record) {
            if ($record['Date Soutenance'] == 'Date Soutenance' || $record['Date Soutenance'] == 'TBD' || $record['Date Soutenance'] == '') {
                continue;
            }

            [$startDateTime, $endDateTime] = $this->parseDateAndTime($record['Date Soutenance'], $record['Heure']);

            $timeslot = Timeslot::firstOrCreate([
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
            ]);

            $room = $this->parseRoom($record['Lieu']);

            // skip if project not found
            if (! $this->pfeIdToProjectId($record['ID PFE'])) {
                // Debugbar::info('Timetable with null project skipped');
                // Filament\Notifications\Notification::make()
                //     ->title('Timetable with null project skipped')
                //     ->danger()
                //     ->send();

                continue;
            }
            $timetable = Timetable::where('project_id', $this->pfeIdToProjectId($record['ID PFE']))->first();

            if ($timetable) {
                $timetable->update([
                    'room_id' => $room->id,
                    'timeslot_id' => $timeslot->id,
                ]);
                $this->checkAnomalies($timetable);
                $this->checkRoom($timeslot, $room, $timetable->id);

            } else {
                $timetable = Timetable::create([
                    'room_id' => $room?->id,
                    'timeslot_id' => $timeslot->id,
                    'project_id' => $this->pfeIdToProjectId($record['ID PFE']),
                ]);
                $this->checkAnomalies($timetable);
                $this->checkRoom($timeslot, $room, $timetable->id);

            }
            // show message on debugbar
            // Debugbar::info('Data imported successfully');

        }
        // Filament\Notifications\Notification::make()
        //     ->title('Data imported successfully')
        //     ->danger()
        //     ->send();
        $this->notifyAnomalies();

    }

    private function parseDateAndTime($dateString, $hourString)
    {
        $frenchToEnglishDays = [
            'lundi' => 'Monday',
            'mardi' => 'Tuesday',
            'mercredi' => 'Wednesday',
            'jeudi' => 'Thursday',
            'vendredi' => 'Friday',
            'samedi' => 'Saturday',
            'dimanche' => 'Sunday',
        ];

        $frenchToEnglishMonths = [
            'janvier' => 'January',
            'février' => 'February',
            'mars' => 'March',
            'avril' => 'April',
            'mai' => 'May',
            'juin' => 'June',
            'juillet' => 'July',
            'août' => 'August',
            'septembre' => 'September',
            'octobre' => 'October',
            'novembre' => 'November',
            'décembre' => 'December',
        ];
        $dateString = strtolower($dateString);

        foreach ($frenchToEnglishDays as $french => $english) {
            $dateString = str_replace($french, $english, $dateString);
        }

        foreach ($frenchToEnglishMonths as $french => $english) {
            $dateString = str_replace($french, $english, $dateString);
        }

        $date = Carbon::createFromFormat('l d F Y', $dateString);
        [$startTime, $endTime] = explode(' - ', $hourString);
        $startHour = substr($startTime, 0, 2);
        $startMinute = substr($startTime, 3, 2);
        $endHour = substr($endTime, 0, 2);
        $endMinute = substr($endTime, 3, 2);

        $startDateTime = Carbon::createFromTime($startHour, $startMinute)->setDateFrom($date);
        $endDateTime = Carbon::createFromTime($endHour, $endMinute)->setDateFrom($date);

        return [$startDateTime, $endDateTime];
    }

    private function parseRoom($roomString)
    {
        if (! $roomString) {
            return Room::where('name', 'Undefined')->first() ?? dd("Room '{$roomString}' not found.");
        }

        // return Room::where('name', $roomString)->firstOrFail();
        return Room::where('name', $roomString)->first() ?? dd("Room '{$roomString}' not found.");
    }

    private function pfeIdToProjectId($pfeId)
    {
        // well check if null
        if (! $pfeId) {
            return null;
        }
        $internshipAgreement = InternshipAgreement::where('id_pfe', $pfeId)->first();
        if (! $internshipAgreement) {
            // throw new \Exception("Project with id_pfe {$pfeId} not found.");
            // we will try to check if project id is imploded with comma

            $pfeIds = explode(',', $pfeId);
            foreach ($pfeIds as $id) {
                $internshipAgreement = InternshipAgreement::where('id_pfe', $id)->first();
                if ($internshipAgreement) {
                    return $internshipAgreement->project_id;
                }
            }
        }

        if ($internshipAgreement->project->defense_status === Enums\DefenseStatus::Authorized || $internshipAgreement->project->defense_status === Enums\DefenseStatus::Completed) {
            return null;
        } else {
            return $internshipAgreement->project_id;
        }
    }

    public function importProfessors()
    {
        foreach ($this->data as $record) {

            $internshipAgreement = InternshipAgreement::where('id_pfe', $record['ID PFE'])->first();
            if ($internshipAgreement) {
                if ($internshipAgreement->project->defense_status === Enums\DefenseStatus::Authorized || $internshipAgreement->project->defense_status === Enums\DefenseStatus::Completed) {
                    continue;
                }
            } elseif (! $internshipAgreement) {
                // Debugbar::info('Internship agreement not found');
                $pfeIds = explode(',', $record['ID PFE']);
                foreach ($pfeIds as $id) {
                    $internshipAgreement = InternshipAgreement::where('id_pfe', $id)->first();
                    if ($internshipAgreement) {
                        if ($internshipAgreement->project->defense_status === Enums\DefenseStatus::Authorized || $internshipAgreement->project->defense_status === Enums\DefenseStatus::Completed) {
                            continue;
                        }
                    }
                }

                continue;
            }

            $project = $internshipAgreement->project;

            if (! $project) {
                // Debugbar::info('Project not found');
                $this->anomalies[] = __('Project with ID PFE :pfeId not found', ['pfeId' => $record['ID PFE']]);

                continue;
            }
            $project->professors()->wherePivot('jury_role', '=', 'Reviewer1')->detach();
            $project->professors()->wherePivot('jury_role', '=', 'Reviewer2')->detach();
            $project->professors()->wherePivot('jury_role', '=', 'Supervisor')->detach();

            $this->importReviewer($project, $record['Nom et Prénom Examinateur 1'], 'Reviewer1');
            $this->importReviewer($project, $record['Nom et Prénom Examinateur 2'], 'Reviewer2');
            $this->ImportSupervisor($project, $record['Encadrant Interne'], 'Supervisor');
        }
    }

    private function importReviewer(Project $project, $reviewerName, $role)
    {
        // skip if reviewer name is empty
        if (! $reviewerName) {
            // Debugbar::info('Reviewer name is empty');
            return;
        }
        $professor = Professor::where('name', $reviewerName)->first();
        if (! $professor) {
            $this->unfoundProfessors[] = 'Reviewer: ' . $reviewerName . ' not found';

            // Debugbar::info('Professor created successfully');
            return;
        }
        if (! $project->professors->contains($professor->id)) {
            $project->professors()->attach($professor->id, ['jury_role' => $role]);
        } else {
            // Debugbar::info('Professor already attached to project');
        }
    }

    private function ImportSupervisor(Project $project, $supervisorName, $role)
    {
        // skip if supervisor name is empty
        if (! $supervisorName) {
            // Debugbar::info('Supervisor name is empty');
            return;
        }
        $professor = Professor::where('name', $supervisorName)->first();
        if (! $professor) {
            $this->unfoundProfessors[] = 'Supervisor: ' . $supervisorName . ' not found';

            // Debugbar::info('Professor created successfully');
            return;
        }
        if (! $project->professors->contains($professor->id)) {
            $project->professors()->attach($professor->id, ['jury_role' => $role]);
        } else {
            // Debugbar::info('Professor already attached to project');
        }
    }

    public function checkRoom($timeslot, $room, $timetableId)
    {
        $is_room_available = RoomService::checkRoomAvailability($timeslot, $room, $timetableId);

        if (! $is_room_available) {
            $this->anomalies[] = __('Room :room is not available in this timeslot :timeslot', ['room' => $room->name, 'timeslot' => $timeslot->start_time]);
        }
    }

    public function checkAnomalies($timetable)
    {
        $professorService = new \App\Services\ProfessorService;
        // $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $timetable->id);
        $unavailableProfessor = $professorService->getUnavailableJury($timetable->timeslot, $timetable->project, $timetable->id);
        $exists = Timetable::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
            ->where('room_id', $timetable->room_id)
            ->where('id', '!=', $timetable->id)
            ->exists();

        // if (! $professor_availability) {
        //     $this->anomalies[] = __('One of professors is not available in this timeslot');

        //     return false;
        // }

        if ($unavailableProfessor) {
            $this->anomalies[] = __('Professor :professor is not available in this timeslot :timeslot', ['professor' => $unavailableProfessor->name, 'timeslot' => $timetable->timeslot->start_time]);

            return false;
        }

        if ($exists) {
            $existingTimetable = Timetable::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
                ->where('room_id', $timetable->room_id)
                ->where('id', '!=', $timetable->id)
                ->first();
            $unavailableProfessor = $professorService->getUnavailableJury($timetable->timeslot, $timetable->project, $timetable->id);
            if ($unavailableProfessor) {
                $this->anomalies[] = __('Professor :professor is not available in this timeslot :timeslot', ['professor' => $unavailableProfessor->name, 'timeslot' => $timetable->timeslot->start_time]);

                return false;
            }

            if ($existingTimetable->project_id !== null) {
                $this->anomalies[] = __('Timeslot :timeslot and Room :room conflict', ['timeslot' => $timetable->timeslot->start_time, 'room' => $timetable->room->name]);

                return false;
            } else {
                return true;
            }
        } else {
            $unavailableProfessor = $professorService->getUnavailableJury($timetable->timeslot, $timetable->project, $timetable->id);
            if ($unavailableProfessor) {
                $this->anomalies[] = __('Professor :professor is not available in this timeslot :timeslot', ['professor' => $unavailableProfessor->name, 'timeslot' => $timetable->timeslot->start_time]);

                return false;
            }
        }

    }

    protected function notifyAnomalies()
    {
        if (! empty($this->anomalies)) {
            $message = implode("\n", [...$this->anomalies, ...$this->unfoundProfessors]); // Concatenate all messages, or format them as you prefer

            // Notification::make()
            //     ->title(__('Anomalies Detected') . $message)
            //     ->danger()
            //     ->persistent()
            //     // ->sendToDatabase(auth()->user());
            //     ->send();
            dd($message);
        }
    }
}
