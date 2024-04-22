<?php

namespace App\Console\Commands;

use App\Models\Timeslot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTimeslots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-timeslots {--start-date=2024-06-24} {--end-date=2024-07-24}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $start_date;

    protected $end_date;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->start_date = $this->option('start-date');
        $this->end_date = $this->option('end-date');
        $this->generateTimeslots();
    }

    protected $dayStartingAt = '09:00:00'; // Assuming a default day start time of 9 AM

    protected $dayEndingAt = '18:00:00'; // Assuming a default day end time of 6 PM

    protected $intervalMinutes = 90; // Assuming a default interval of 90 minutes

    protected $workingDays = [1, 2, 3, 4, 5]; // Assuming Monday to Friday as working days

    public function generateWorkingDays()
    {
        $startDate = $this->start_date ? Carbon::parse($this->start_date) : now()->startOfMonth();
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : now()->endOfMonth();
        $workingDays = $this->filterWorkingDays($this->generateDates($startDate, $endDate));

        return $workingDays;
    }

    public function generateDates(Carbon $startDate, Carbon $endDate)
    {
        $dates = collect();

        while ($startDate->lte($endDate)) {
            $dates->push($startDate->copy());
            $startDate->addDay();
        }

        return $dates;
    }

    public function filterWorkingDays($dates)
    {
        return $dates->filter(function ($date) {
            return in_array($date->dayOfWeek, $this->workingDays);
        });
    }

    public function generateTimeslotsForDay($date)
    {

        $timeslots = collect();
        $dayStartingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayStartingAt);
        $dayEndingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayEndingAt);

        while ($dayStartingAt->lt($dayEndingAt)) {
            $timeslots->push($dayStartingAt->copy());
            $dayStartingAt->addMinutes($this->intervalMinutes);
        }

        return $timeslots;
    }

    public function generateTimeslots()
    {
        $this->info('Generating timeslots');
        $workingDays = $this->generateWorkingDays();
        $timeslots = $workingDays->flatMap(function ($date) {
            return $this->generateTimeslotsForDay($date);
        });

        $timeslots->each(function ($timeslot, $index) use ($timeslots) {
            $start_time = $timeslot;
            $end_time = $timeslots->get($index + 1);

            if ($end_time) {
                $interval = $start_time->diffInMinutes($end_time);

                if ($interval == 90) {
                    $timeslotPeriod = new Timeslot($start_time, $end_time);
                    $timeslotPeriod->is_enabled = 1;
                    $timeslotPeriod->save();
                }
            }
        });

        $this->info('Timeslots generated successfully');

    }
}
