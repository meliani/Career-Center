<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Timeslot;
use Carbon\CarbonPeriod;

class AutoScheduleService
{
    protected $dayStartingAt = '09:00:00'; // Assuming a default day start time of 9 AM
    protected $dayEndingAt = '18:00:00'; // Assuming a default day end time of 6 PM
    protected $intervalMinutes = 90; // Assuming a default interval of 90 minutes
    protected $workingDays = [1, 2, 3, 4, 5]; // Assuming Monday to Friday as working days

    public function generateWorkingDays($ScheduleParameters)
    {
        // $startDate = now()->startOfMonth();
        // $endDate = now()->endOfMonth();
        $startDate = $ScheduleParameters->schedule_starting_at;
        $endDate = $ScheduleParameters->schedule_ending_at;
        $workingDays = $this->filterWorkingDays($this->generateDates($startDate, $endDate));

        return $workingDays;
    }

    protected function generateDates(Carbon $startDate, Carbon $endDate)
    {
        $dates = collect();

        while ($startDate->lte($endDate)) {
            $dates->push($startDate->copy());
            $startDate->addDay();
        }

        return $dates;
    }

    protected function filterWorkingDays(Collection $dates)
    {
        return $dates->filter(function ($date) {
            return in_array($date->dayOfWeek, $this->workingDays);
        });
    }

    public function generateTimeslots($ScheduleParameters): Collection
    {
        $workingDays = $this->generateWorkingDays($ScheduleParameters);

        $timeslots = $workingDays->flatMap(function ($date) {
            return $this->generateTimeslotsForDay($date);
        });

        return $timeslots;
    }

    protected function generateTimeslotsForDay(Carbon $date): Collection
    {
        // $timeslots = New Timeslot();
        $timeslots = collect();
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayStartingAt);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayEndingAt);

        while ($startTime->lt($endTime)) {
            $timeslots->push($startTime->copy());
            $startTime->addMinutes($this->intervalMinutes);
        }

        return $timeslots;
    }

    protected function isWithinDayTime(Carbon $dateTime)
    {
        $dayStartTime = Carbon::parse($dateTime->format('Y-m-d') . ' ' . $this->dayStartingAt, 'UTC');
        $dayEndTime = Carbon::parse($dateTime->format('Y-m-d') . ' ' . $this->dayEndingAt, 'UTC');

        return $dateTime->between($dayStartTime, $dayEndTime, true); // Use inclusive comparison
    }
}
