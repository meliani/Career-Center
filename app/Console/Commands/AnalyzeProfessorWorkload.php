<?php

namespace App\Console\Commands;

use App\Models\Professor;
use App\Models\Project;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnalyzeProfessorWorkload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-professor-workload
                            {--year-id= : Academic year ID (defaults to current year)}
                            {--threshold=3 : Maximum number of defenses per day}
                            {--format=table : Output format (table, json, csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze professor workload to identify potential scheduling issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get parameters
        $yearId = $this->option('year-id') ?: Year::current()->id;
        $threshold = $this->option('threshold');
        $format = $this->option('format');
        
        // Get all professors with scheduled defenses
        $professors = Professor::whereHas('projects', function ($query) use ($yearId) {
            $query->whereHas('timetable', function ($q) use ($yearId) {
                $q->where('year_id', $yearId);
            });
        })->get();
        
        if ($professors->isEmpty()) {
            $this->error("No professors with scheduled defenses found for the specified year.");
            return 1;
        }
        
        $this->info("Analyzing workload for {$professors->count()} professors...");
        
        // Analyze workload for each professor
        $results = [];
        
        foreach ($professors as $professor) {
            // Get all projects for this professor that have been scheduled
            $scheduledProjects = $professor->projects()
                ->whereHas('timetable', function ($query) use ($yearId) {
                    $query->where('year_id', $yearId);
                })
                ->with('timetable.timeslot')
                ->get();
                
            if ($scheduledProjects->isEmpty()) {
                continue;
            }
            
            // Group defenses by date
            $defensesByDate = [];
            
            foreach ($scheduledProjects as $project) {
                $timetable = $project->timetable;
                if (!$timetable || !$timetable->timeslot) {
                    continue;
                }
                
                $date = $timetable->timeslot->start_time->format('Y-m-d');
                
                if (!isset($defensesByDate[$date])) {
                    $defensesByDate[$date] = [];
                }
                
                $defensesByDate[$date][] = [
                    'project_id' => $project->id,
                    'project_title' => $project->title,
                    'time' => $timetable->timeslot->start_time->format('H:i'),
                    'room' => $timetable->room->name ?? 'Unknown room',
                ];
            }
            
            // Find dates with too many defenses
            $overloadedDates = [];
            
            foreach ($defensesByDate as $date => $defenses) {
                if (count($defenses) > $threshold) {
                    $overloadedDates[$date] = count($defenses);
                }
            }
            
            if (!empty($overloadedDates)) {
                $results[] = [
                    'professor_id' => $professor->id,
                    'professor_name' => $professor->full_name,
                    'total_defenses' => $scheduledProjects->count(),
                    'overloaded_dates' => $overloadedDates,
                    'details' => $defensesByDate,
                ];
            }
        }
        
        // Output results
        if (empty($results)) {
            $this->info("No professors with excessive workload found.");
            return 0;
        }
        
        $this->info("Found " . count($results) . " professors with potential workload issues:");
        
        if ($format === 'json') {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
        } elseif ($format === 'csv') {
            $this->outputCsv($results);
        } else {
            $this->outputTable($results);
        }
        
        return 0;
    }
    
    protected function outputTable(array $results)
    {
        $tableRows = [];
        
        foreach ($results as $result) {
            $overloadedDatesStr = [];
            foreach ($result['overloaded_dates'] as $date => $count) {
                $overloadedDatesStr[] = "{$date}: {$count} defenses";
            }
            
            $tableRows[] = [
                'Professor' => $result['professor_name'],
                'Total Defenses' => $result['total_defenses'],
                'Overloaded Dates' => implode("\n", $overloadedDatesStr),
            ];
        }
        
        $this->table(
            ['Professor', 'Total Defenses', 'Overloaded Dates'],
            $tableRows
        );
    }
    
    protected function outputCsv(array $results)
    {
        $headers = ['professor_id', 'professor_name', 'total_defenses', 'date', 'defense_count'];
        $this->line(implode(',', $headers));
        
        foreach ($results as $result) {
            foreach ($result['overloaded_dates'] as $date => $count) {
                $row = [
                    $result['professor_id'],
                    $result['professor_name'],
                    $result['total_defenses'],
                    $date,
                    $count,
                ];
                $this->line(implode(',', $row));
            }
        }
    }
}
