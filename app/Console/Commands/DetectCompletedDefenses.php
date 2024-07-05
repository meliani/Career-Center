<?php

namespace App\Console\Commands;

use App\Enums;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DetectCompletedDefenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:detect-completed-defenses';

    protected $description = 'Detect projects with Authorized status and update to Completed if authorized.';

    public function handle()
    {
        Log::info('Starting update-status command');

        $today = Carbon::today();
        $projects = Project::where('defense_status', Enums\DefenseStatus::Authorized)
            ->whereHas('timetable', function ($query) use ($today) {
                // Assuming 'timetable' is the relationship name and 'timeslot' is a date field
                $query->whereHas('timeslot', function ($query) use ($today) {
                    $query->whereDate('start_time', $today);
                });
            })->get();

        $completedProjects = collect();

        foreach ($projects as $project) {
            if ($project->isAuthorized()) {
                $project->defense_status = Enums\DefenseStatus::Completed;
                $project->save();
                $completedProjects->push($project);
                $this->info("Project ID {$project->id} status updated to achieved.");
            }
        }
        Log::info('Finished update-status command');

        // Send email if there are completed projects
        if ($completedProjects->isNotEmpty()) {
            // $administrators = \App\Models\User::administrators()->pluck('email');
            $administration = \App\Models\User::where('id', 7)->get('email');
            $AdministrativeSupervisors = \App\Models\User::where('role', \App\Enums\Role::AdministrativeSupervisor->value)
                ->pluck('email');
            \Mail::to($AdministrativeSupervisors->merge($administration))->send(new \App\Mail\CompletedDefensesNotification($completedProjects));
            Log::info('Completed defenses email sent.');
        }

    }
}
