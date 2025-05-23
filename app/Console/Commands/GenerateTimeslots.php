<?php

namespace App\Console\Commands;

use App\Models\Timeslot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class GenerateTimeslots extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-timeslots {--start-date=2024-06-24} {--end-date=2024-07-24} {--year-id=} {--day-start=} {--day-end=} {--lunch-start=} {--lunch-end=} {--interval=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate timeslots for the given date range and academic year';

    protected $start_date;
    protected $end_date;
    protected $year_id;
    protected $dayStartingAt;
    protected $dayEndingAt;
    protected $lunchStartingAt;
    protected $lunchEndingAt;
    protected $intervalMinutes;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Parse dates from options
            $this->start_date = Carbon::parse($this->option('start-date'));
            $this->end_date = Carbon::parse($this->option('end-date'));
            $this->year_id = $this->option('year-id') ?: \App\Models\Year::current()->id;
            
            // Get schedule parameters from command options or use defaults
            $this->dayStartingAt = $this->option('day-start') ?: '09:00:00';
            $this->dayEndingAt = $this->option('day-end') ?: '18:00:00';
            $this->lunchStartingAt = $this->option('lunch-start') ?: '12:00:00';
            $this->lunchEndingAt = $this->option('lunch-end') ?: '13:00:00';
            $this->intervalMinutes = (int)($this->option('interval') ?: 90);
            
            $this->info("========================================");
            $this->info("GENERATING TIMESLOTS WITH THESE PARAMETERS:");
            $this->info("========================================");
            $this->info("Date Range: {$this->start_date->format('Y-m-d')} to {$this->end_date->format('Y-m-d')}");
            $this->info("Day Hours: {$this->dayStartingAt} to {$this->dayEndingAt}");
            $this->info("Lunch Break: {$this->lunchStartingAt} to {$this->lunchEndingAt}");
            $this->info("Timeslot Duration: {$this->intervalMinutes} minutes");
            $this->info("Year ID: {$this->year_id}");
            $this->info("========================================");
            
            // Calculate expected number of timeslots for verification
            $workingDays = $this->generateWorkingDays()->count();
            $this->info("Found {$workingDays} working days in the date range");
            
            $this->generateTimeslots();
            
            // Count how many were actually created
            $actualTimeslots = \App\Models\Timeslot::where('year_id', $this->year_id)
                ->whereBetween('start_time', [$this->start_date, $this->end_date->endOfDay()])
                ->count();
                
            $this->info("========================================");
            $this->info("GENERATION COMPLETE: {$actualTimeslots} timeslots created");
            $this->info("========================================");
            
        } catch (\Exception $e) {
            $this->error("Error generating timeslots: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    protected $workingDays = [1, 2, 3, 4, 5]; // Assuming Monday to Friday as working days

    public function generateWorkingDays()
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $workingDays = $this->filterWorkingDays($this->generateDates($startDate, $endDate));

        return $workingDays;
    }

    public function generateDates(Carbon $startDate, Carbon $endDate)
    {
        $dates = collect();
        $endDate->endOfDay();

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
        
        try {
            $dayStartingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayStartingAt);
            $dayEndingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $this->dayEndingAt);
            
            // Default lunch times if not provided
            $lunchStartString = $this->lunchStartingAt ?: '12:00:00';
            $lunchEndString = $this->lunchEndingAt ?: '13:00:00';
            
            $lunchStartingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $lunchStartString);
            $lunchEndingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $lunchEndString);
            
            $this->info("Generating timeslots for {$date->format('Y-m-d')}:");
            $this->info("  Day: {$dayStartingAt->format('H:i')} to {$dayEndingAt->format('H:i')}");
            $this->info("  Lunch: {$lunchStartingAt->format('H:i')} to {$lunchEndingAt->format('H:i')}");
            $this->info("  Interval: {$this->intervalMinutes} minutes");

            // Generate morning timeslots (before lunch)
            $currentTime = $dayStartingAt->copy();
            while ($currentTime->lt($lunchStartingAt)) {
                $endTime = $currentTime->copy()->addMinutes($this->intervalMinutes);
                
                // If the timeslot would extend into lunch time, stop before lunch
                if ($endTime->gt($lunchStartingAt)) {
                    break;
                }
                
                $timeslots->push($currentTime->copy());
                $this->info("Added morning timeslot: {$currentTime->format('H:i')}");
                $currentTime->addMinutes($this->intervalMinutes);
            }

            // Generate afternoon timeslots (after lunch)
            $currentTime = $lunchEndingAt->copy();
            while ($currentTime->lt($dayEndingAt)) {
                $endTime = $currentTime->copy()->addMinutes($this->intervalMinutes);
                
                // If the timeslot would extend past the end of day, stop
                if ($endTime->gt($dayEndingAt)) {
                    break;
                }
                
                $timeslots->push($currentTime->copy());
                $this->info("Added afternoon timeslot: {$currentTime->format('H:i')}");
                $currentTime->addMinutes($this->intervalMinutes);
            }
            
            $this->info("Generated {$timeslots->count()} timeslots for {$date->format('Y-m-d')}");
            
        } catch (\Exception $e) {
            $this->error("Error generating timeslots for day {$date->format('Y-m-d')}: " . $e->getMessage());
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

                if ($interval == $this->intervalMinutes) {
                    $timeslotPeriod = new Timeslot($start_time, $end_time);
                    $timeslotPeriod->is_enabled = 1;
                    $timeslotPeriod->year_id = $this->year_id;
                    $timeslotPeriod->save();

                    $this->info('Timeslot generated: ' . $timeslotPeriod->start_time . ' - ' . $timeslotPeriod->end_time . ' (Year ID: ' . $this->year_id . ')');
                }
            }
        });

        $this->info('Timeslots generated successfully');

    }
}
