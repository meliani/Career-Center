<?php

namespace App\Services;

use App\Models\Room;
// use Spatie\Period\Period;
// use Spatie\Period\Precision;
use App\Models\ScheduleParameters;
// use Spatie\Period\PeriodCollection;
use App\Models\Timeslot;
use App\Models\Timetable;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Console\Scheduling\Schedule;

class TimeslotService
{
    /*  parameters table
    'schedule_starting_at',
    'schedule_ending_at',
    'day_starting_at',
    'day_ending_at',
    'number_of_rooms',
    'max_defenses_per_professor',
    'max_rooms',
    'minutes_per_slot', */

    protected int $number_of_rooms;

    protected int $max_defenses_per_professor;

    protected int $max_rooms;

    protected int $minutes_per_slot;

    protected CarbonPeriod $period;

    protected $timeslot;

    protected $day;

    protected Room $room;

    protected Room $rooms;

    protected $scheduleParameters;

    protected $schedule_starting_at;

    protected $schedule_ending_at;

    protected $day_starting_at;

    protected $day_ending_at;

    protected $all_timeslots;

    protected $day_timeslots;

    protected CarbonPeriod $periods;

    // protected PeriodCollection $all_timeslots;
    // protected PeriodCollection $day_timeslots;
    // protected PeriodCollection $periods;
    // protected PeriodCollection $timeslots;

    protected $timeslots;

    public function __construct(?ScheduleParameters $scheduleParameters = null)
    {
        $this->scheduleParameters = $scheduleParameters;
        if ($this->scheduleParameters == null) {
            $scheduleParameters = ScheduleParameters::first();
        } else {
            $scheduleParameters = $this->scheduleParameters;
        }
        $this->schedule_starting_at = $scheduleParameters->schedule_starting_at;
        $this->schedule_ending_at = $scheduleParameters->schedule_ending_at;
        $this->day_starting_at = $scheduleParameters->day_starting_at;
        $this->day_ending_at = $scheduleParameters->day_ending_at;
        $this->number_of_rooms = $scheduleParameters->number_of_rooms;
        $this->max_defenses_per_professor = $scheduleParameters->max_defenses_per_professor;
        $this->max_rooms = $scheduleParameters->max_rooms;
        $this->minutes_per_slot = $scheduleParameters->minutes_per_slot;
        // $this->all_timeslots = new PeriodCollection();
        // $this->day_timeslots = new PeriodCollection();
        // $this->periods = new PeriodCollection();
        // $this->period = [];
        // $this->timeslot = [];
        // $this->timeslots = new PeriodCollection();
        // $this->day = [];
        // $this->room = [];
        // $this->rooms = [];
    }

    public function generateDayTimeslots($day)
    // : PeriodCollection
    {
        // // using slotslitter service
        // $period = Period::make($this->scheduleParameters->day_starting_at, $this->scheduleParameters->day_ending_at, Precision::Minute());
        // // dd($period);
        // // $periods->excludeEndDate();
        // $periods = new SlotSplitter($period);
        // $periods->split($this->minutes_per_slot);
        // return $periods;
        $startPeriod = $this->scheduleParameters->day_starting_at;
        $endPeriod = $this->scheduleParameters->day_ending_at;

        $period = CarbonPeriod::create($startPeriod, '90 minutes', $endPeriod);
        $day_timeslots = [];

        foreach ($period as $date) {
            $day_timeslots[] = $date->format('H:i');
        }

        return $day_timeslots;
        // dd($period);
    }

    public function generateAllTimeslots()
    {
        $this->generateDayTimeslots();

        $this->all_timeslots = [];

        if ($this->schedule_starting_at && $this->schedule_ending_at) {
            // $period = Period::make($this->schedule_starting_at, $this->schedule_ending_at, Precision::Day());
            $days = CarbonPeriod::create($this->schedule_starting_at, '1 day', $this->schedule_starting_at);
            foreach ($days as $day) {
                $day = $day->format('Y-m-d');
                $day_timeslots = $this->generateDayTimeslots($day);
                // dd($day);
                $this->all_timeslots[] = $this->day_timeslots;
            }
            dd($this->all_timeslots);

        }

        return $this->all_timeslots;
    }

    public function saveTimeslots($timeslots): void
    {
        // Check if $timeslots is a PeriodCollection and contains Period objects
        if ($timeslots instanceof PeriodCollection && ! $timeslots->isEmpty()) {
            // Save PeriodCollection to database
            foreach ($timeslots as $timeslot) {
                Timeslot::create([
                    'start_time' => $timeslot->start(),
                    'end_time' => $timeslot->end(),
                ]);
            }
        } else {
            // Handle the case where $timeslots is not a PeriodCollection or is empty
            throw new Exception('Timeslots must be a non-empty PeriodCollection');
        }
    }

    public static function checkTimeslotAvailability(Timeslot $timeslot, Room $room)
    {
        // Check if the timeslot is available in the room
        $timetable = Timetable::where('timeslot_id', $timeslot->id)
            ->where('room_id', $room->id)
            ->first();

        return $timetable === null;
    }
}
