<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        dd($project);
        if ($project->agreements->isEmpty()) {
            // Project has no agreements, so we can safely delete it
            $project->delete();

            Filament\Notifications\Notification::make()
                ->title('Empty project :organiazation - :title deleted', [
                    'organization' => $project->organization->name,
                    'title' => $project->title,
                ])
                ->info()
                ->send()
                ->sendToDatabase();
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        $project->timetable()->delete();
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
