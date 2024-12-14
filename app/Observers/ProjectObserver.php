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
                ->title('Empty project removed')
                ->info()
                ->send()
                ->toDatabase();
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        // detach timetable
        /*     public function timetable()
    {
        return $this->hasOne(Timetable::class);
    } */
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
