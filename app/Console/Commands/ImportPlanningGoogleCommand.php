<?php

namespace App\Console\Commands;

use App\Facades\GlobalDefenseCalendarConnector;
use App\Models\InternshipAgreement;
use App\Models\Planning;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\Timetable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command; // Assuming you have a Planning model

// Using the league/csv package for CSV handling

class ImportPlanningGoogleCommand extends Command
{
    protected $signature = 'planning:import-google';

    protected $description = 'Imports planning data from a google drive';

    public function handle()
    {

        $connector = new GlobalDefenseCalendarConnector();
        $data = $connector->getDefenses();
        // dd($data);
        // $data->setHeaderOffset(0); // Assuming the first row contains the headers
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
        foreach ($data as $record) {
            // skip first row

            if ($record['Date Soutenance'] == 'Date Soutenance') {
                continue;
            }
            $startDateTime = Carbon::now();

            // lets import date and time to timeslots table
            // timeslot contains start_time and end_time (datetime fields)
            // data sample : date="Lundi 24 juin 2024" hour="09H00 - 10H30" we need to format it before object creation
            Carbon::setLocale('fr');
            setlocale(LC_TIME, 'fr_FR.UTF-8');
            $dateString = $record['Date Soutenance'];
            $hourString = $record['Heure'];

            // set date from $dateString to $startDateTime

            // $startDateTime->setDateFrom($dateString);

            [$startTime, $endTime] = explode(' - ', $hourString);
            $startHour = substr($startTime, 0, 2);
            $startMinute = substr($startTime, 3, 2);
            $endHour = substr($endTime, 0, 2);
            $endMinute = substr($endTime, 3, 2);

            // Set start time on $startDateTime
            $startDateTime->setTime($startHour, $startMinute);

            // Clone $startDateTime to create $endDateTime and set end time
            $endDateTime = clone $startDateTime;
            $endDateTime->setTime($endHour, $endMinute);

            foreach ($frenchToEnglishDays as $french => $english) {
                $dateString = str_replace($french, $english, $dateString);
            }

            foreach ($frenchToEnglishMonths as $french => $english) {
                $dateString = str_replace($french, $english, $dateString);
            }

            // Step 4: Create a Carbon instance from the modified date string
            $date = \Carbon\Carbon::createFromFormat('l d F Y', $dateString);

            $startDateTime->setDateFrom($date);
            $endDateTime->setDateFrom($date);

            $timeslot = Timeslot::FirstOrCreate([
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
            ]);

            // Step 5: Create a Planning instance and associate it with the Timeslot
            /* timeslot_id
            room_id
            project_id */
            $room = $this->parseRoom($record['Lieu']);

            Timetable::create([
                'room_id' => $room->id,
                'timeslot_id' => $timeslot->id,
                'project_id' => $this->PfeIdToProjectId($record['ID PFE']),

            ]);

            $this->info("Imported: {$date} on {$record['Date Soutenance']}");
        }

        $this->info('Import completed successfully.');

        return 0;
    }

    private function parseRoom($roomString)
    {
        return Room::where('name', $roomString)->firstOrFail();
    }

    private function PfeIdToProjectId($pfeId)
    {
        // check if the id exists
        if (! InternshipAgreement::where('id_pfe', $pfeId)->exists()) {
            $this->error("Project with id_pfe {$pfeId} not found.");

            return null;
        }

        return InternshipAgreement::where('id_pfe', $pfeId)->first()->project_id;
    }
}
