<?php

namespace App\Services;

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
        $this->connector = new GlobalDefenseCalendarConnector();
        $this->data = $this->connector->getDefenses();

    }

    public function importData()
    {
        foreach ($this->data as $record) {
            if ($record['Date Soutenance'] == 'Date Soutenance') {
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
                Filament\Notifications\Notification::make()
                    ->title('Timetable with null project skipped')
                    ->danger()
                    ->send();

                continue;
            }
            $timetable = Timetable::where('project_id', $this->pfeIdToProjectId($record['ID PFE']))->first();

            if ($timetable) {
                $timetable->update([
                    'room_id' => $room?->id,
                    'timeslot_id' => $timeslot->id,
                ]);
            } else {

                $timetable = Timetable::create([
                    'room_id' => $room?->id,
                    'timeslot_id' => $timeslot->id,
                    'project_id' => $this->pfeIdToProjectId($record['ID PFE']),
                ]);
                $this->checkAnomalies($timetable);

            }
            // show message on debugbar
            // Debugbar::info('Data imported successfully');

        }
        Filament\Notifications\Notification::make()
            ->title('Data imported successfully')
            ->danger()
            ->send();
        $this->notifyAnomalies();

    }

    private function parseDateAndTime($dateString, $hourString)
    {
        $frenchToEnglishDays = [
            'Lundi' => 'Monday',
            'Mardi' => 'Tuesday',
            'Mercredi' => 'Wednesday',
            'Jeudi' => 'Thursday',
            'Vendredi' => 'Friday',
            'Samedi' => 'Saturday',
            'Dimanche' => 'Sunday',
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
            'Septembre' => 'September',
            'octobre' => 'October',
            'novembre' => 'November',
            'décembre' => 'December',
        ];

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
            return null;
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

        return $internshipAgreement->project_id;
    }

    public function importProfessors()
    {
        foreach ($this->data as $record) {
            $internshipAgreement = InternshipAgreement::where('id_pfe', $record['ID PFE'])->first();
            if (! $internshipAgreement) {
                // Debugbar::info('Internship agreement not found');
                $pfeIds = explode(',', $record['ID PFE']);
                foreach ($pfeIds as $id) {
                    $internshipAgreement = InternshipAgreement::where('id_pfe', $id)->first();
                    if ($internshipAgreement) {
                        return $internshipAgreement->project_id;
                    }
                }

                continue;
            }

            $project = $internshipAgreement->project;
            if (! $project) {
                // Debugbar::info('Project not found');

                continue;
            }

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
            $this->unfoundProfessors[] = $reviewerName;

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
            $this->unfoundProfessors[] = $supervisorName;

            // Debugbar::info('Professor created successfully');
            return;
        }

        if (! $project->professors->contains($professor->id)) {
            $project->professors()->attach($professor->id, ['jury_role' => $role]);
        } else {
            // Debugbar::info('Professor already attached to project');
        }
    }

    public function checkAnomalies($timetable)
    {
        $professorService = new \App\Services\ProfessorService;
        $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $timetable->id);

        $exists = Timetable::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
            ->where('room_id', $timetable->room_id)
            ->where('id', '!=', $timetable->id)
            ->exists();

        if (! $professor_availability) {
            $this->anomalies[] = __('One of professors is not available in this timeslot, your operation is aborted');

            return false;
        }

        if ($exists) {
            $existingTimetable = Timetable::withoutGlobalScopes()->where('timeslot_id', $timetable->timeslot_id)
                ->where('room_id', $timetable->room_id)
                ->where('id', '!=', $timetable->id)
                ->first();
            $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $existingTimetable->id);
            if (! $professor_availability) {
                $this->anomalies[] = __('One of professors is not available in this timeslot, your operation is aborted');

                return false;
            }

            if ($existingTimetable->project_id !== null) {
                $this->anomalies[] = __('Timeslot :timeslot and Room :room conflict, your operation is aborted', ['timeslot' => $timetable->timeslot->start_time, 'room' => $timetable->room->name]);

                return false;
            } else {
                return true;
            }
        } else {
            $professor_availability = $professorService->checkJuryAvailability($timetable->timeslot, $timetable->project, $timetable->id);
            if (! $professor_availability) {
                $this->anomalies[] = __('One of professors is not available in this timeslot, your operation is aborted');

                return false;
            }
        }

    }

    protected function notifyAnomalies()
    {
        if (! empty($this->anomalies)) {
            $message = implode("\n", $this->anomalies); // Concatenate all messages, or format them as you prefer
            Notification::make()
                ->title(__('Anomalies Detected') . $message)
                ->danger()
                ->persistent()
                // ->sendToDatabase(auth()->user());
                ->send();
        }
    }
}