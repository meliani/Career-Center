<?php

namespace App\Console\Commands;

use App\Enums;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DetectAssuredDefenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:detect-assured-defenses';

    protected $description = 'Update status of projects based on their timetable and authorization.';

    public function handle()
    {
        Log::info('Starting update-status command');

        // $today = Carbon::today();
        // $projects = Project::whereHas('timetable', function ($query) use ($today) {
        //     // Assuming 'timetable' is the relationship name and 'timeslot' is a date field
        //     $query->whereHas('timeslot', function ($query) use ($today) {
        //         $query->whereDate('start_time', $today);
        //     });
        // })->get();

        // foreach ($projects as $project) {
        //     // Assuming 'isAuthorized' is a method or attribute to check authorization
        //     if ($project->isAuthorized()) {
        //         $project->defense_status = Enums\DefenseStatus::Completed;
        //         $project->save();
        //         $this->info("Project ID {$project->id} status updated to achieved.");
        //     }
        // }
        Log::info('Finished update-status command');
    }
}
