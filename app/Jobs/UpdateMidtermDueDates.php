<?php

namespace App\Jobs;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateMidtermDueDates implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'update_midterm_due_dates';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting midterm due date updates');
        
        // Get all projects with start and end dates set
        $projects = Project::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();
            
        $updatedCount = 0;
        
        foreach ($projects as $project) {
            try {
                // Calculate midterm date as the middle point between start and end dates
                if ($project->start_date && $project->end_date) {
                    $startDate = Carbon::parse($project->start_date);
                    $endDate = Carbon::parse($project->end_date);
                    
                    // Calculate the middle point (midterm)
                    $durationInDays = $endDate->diffInDays($startDate);
                    $midtermDueDate = $startDate->copy()->addDays(intval($durationInDays / 2));
                    
                    // Only update if the midterm date is different or not set
                    if ($project->midterm_due_date === null || $midtermDueDate->ne(Carbon::parse($project->midterm_due_date))) {
                        $project->midterm_due_date = $midtermDueDate;
                        $project->save();
                        $updatedCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error updating midterm date for project ID: ' . $project->id . ': ' . $e->getMessage());
            }
        }
        
        Log::info("Midterm due dates update completed. Updated {$updatedCount} projects.");
    }
}
