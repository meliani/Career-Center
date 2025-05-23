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
            // Store only the time component without any date part
            $this->dayStartingAt = $this->option('day-start') ?: '09:00:00';
            $this->dayEndingAt = $this->option('day-end') ?: '18:00:00';
            $this->lunchStartingAt = $this->option('lunch-start') ?: '12:00:00';
            $this->lunchEndingAt = $this->option('lunch-end') ?: '13:00:00';
            $this->intervalMinutes = (int)($this->option('interval') ?: 90);
            
            // Validate time formats
            try {
                Carbon::parse($this->dayStartingAt);
                Carbon::parse($this->dayEndingAt);
                Carbon::parse($this->lunchStartingAt);
                Carbon::parse($this->lunchEndingAt);
            } catch (\Exception $e) {
                $this->error("Invalid time format: " . $e->getMessage());
                return;
            }
            
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

    public function generateWorkingDays()
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        
        $this->info("Finding working days between {$startDate->format('Y-m-d')} and {$endDate->format('Y-m-d')}");
        
        $workingDays = $this->filterWorkingDays($this->generateDates($startDate, $endDate));
        
        $this->info("Found {$workingDays->count()} working days:");
        foreach ($workingDays as $day) {
            $this->info(" - {$day->format('Y-m-d')} ({$day->format('l')})");
        }

        return $workingDays;
    }

    public function generateDates(Carbon $startDate, Carbon $endDate)
    {
        $dates = collect();
        $currentDate = $startDate->copy()->startOfDay(); // Ensure we start from the beginning of the day
        $endDate = $endDate->copy()->endOfDay(); // Ensure we include the entire last day
        
        $this->info("Generating dates from {$currentDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        
        while ($currentDate->lte($endDate)) {
            $dates->push($currentDate->copy());
            $currentDate->addDay();
        }

        $this->info("Generated {$dates->count()} dates");
        return $dates;
    }

    public function filterWorkingDays($dates)
    {
        // Monday=1, Tuesday=2, Wednesday=3, Thursday=4, Friday=5, Saturday=6, Sunday=0
        $workingDays = collect([1, 2, 3, 4, 5]); // Monday through Friday
        
        $filtered = $dates->filter(function ($date) use ($workingDays) {
            $isWorkingDay = $workingDays->contains($date->dayOfWeek);
            $dayName = $date->format('l');
            $this->info(" - {$date->format('Y-m-d')} ({$dayName}) is " . ($isWorkingDay ? "a working day" : "NOT a working day"));
            return $isWorkingDay;
        });
        
        return $filtered;
    }

    public function generateTimeslotsForDay($date)
    {
        $timeslots = collect();
        
        try {
            // Extract time parts only, without using current date
            $dayStartTime = Carbon::parse($this->dayStartingAt)->format('H:i:s');
            $dayEndTime = Carbon::parse($this->dayEndingAt)->format('H:i:s');
            $lunchStartTime = Carbon::parse($this->lunchStartingAt)->format('H:i:s');
            $lunchEndTime = Carbon::parse($this->lunchEndingAt)->format('H:i:s');
            
            // Combine with the specific day's date
            $dayStartingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $dayStartTime);
            $dayEndingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $dayEndTime);
            $lunchStartingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $lunchStartTime);
            $lunchEndingAt = Carbon::parse($date->format('Y-m-d') . ' ' . $lunchEndTime);
            
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
                    $this->info("  Skipping timeslot that would overlap with lunch: {$currentTime->format('H:i')} - {$endTime->format('H:i')}");
                    break;
                }
                
                $this->info("  Adding morning slot: {$currentTime->format('H:i')} - {$endTime->format('H:i')}");
                $timeslots->push($currentTime->copy());
                $currentTime->addMinutes($this->intervalMinutes);
            }

            // Generate afternoon timeslots (after lunch)
            $currentTime = $lunchEndingAt->copy();
            while ($currentTime->lt($dayEndingAt)) {
                $endTime = $currentTime->copy()->addMinutes($this->intervalMinutes);
                
                // If the timeslot would extend past the end of day, stop
                if ($endTime->gt($dayEndingAt)) {
                    $this->info("  Skipping timeslot that would go past end of day: {$currentTime->format('H:i')} - {$endTime->format('H:i')}");
                    break;
                }
                
                $this->info("  Adding afternoon slot: {$currentTime->format('H:i')} - {$endTime->format('H:i')}");
                $timeslots->push($currentTime->copy());
                $currentTime->addMinutes($this->intervalMinutes);
            }
            
            $this->info("Generated {$timeslots->count()} timeslots for {$date->format('Y-m-d')}");
            
        } catch (\Exception $e) {
            $this->error("Error generating timeslots for day {$date->format('Y-m-d')}: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }

        return $timeslots;
    }

    public function generateTimeslots()
    {
        $this->info('Generating timeslots');
        $workingDays = $this->generateWorkingDays();
        
        $this->info("Found {$workingDays->count()} working days in the date range");
        
        // Track how many timeslots were created
        $createdCount = 0;
        
        // Generate timeslots for each working day
        foreach ($workingDays as $date) {
            $dayTimeslots = $this->generateTimeslotsForDay($date);
            
            $this->info("Processing {$dayTimeslots->count()} timeslots for {$date->format('Y-m-d')}");
            
            // For each timeslot start time, create a timeslot record
            foreach ($dayTimeslots as $startTime) {
                try {
                    // Calculate end time based on interval
                    $endTime = (clone $startTime)->addMinutes($this->intervalMinutes);
                    
                    $this->info("Creating timeslot: {$startTime->format('Y-m-d H:i:s')} to {$endTime->format('Y-m-d H:i:s')}");
                    
                    // Check if a similar timeslot already exists to avoid duplicates
                    $existingTimeslot = \App\Models\Timeslot::where('start_time', $startTime)
                        ->where('end_time', $endTime)
                        ->where('year_id', $this->year_id)
                        ->first();
                        
                    if ($existingTimeslot) {
                        $this->warn("Timeslot already exists: {$startTime->format('Y-m-d H:i')} - {$endTime->format('Y-m-d H:i')}");
                        continue;
                    }
                    
                    // Use create() instead of the constructor and save()
                    $timeslot = \App\Models\Timeslot::create([
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_enabled' => true,
                        'year_id' => $this->year_id,
                    ]);
                    
                    if ($timeslot && $timeslot->id) {
                        $this->info("Saved timeslot: {$timeslot->start_time} - {$timeslot->end_time} (ID: {$timeslot->id})");
                        $createdCount++;
                    } else {
                        $this->error("Failed to create timeslot");
                    }
                } catch (\Exception $e) {
                    $this->error("Error creating timeslot: " . $e->getMessage());
                    $this->error($e->getTraceAsString());
                }
            }
        }

        $this->info("Generated a total of {$createdCount} timeslots successfully");
    }
}
