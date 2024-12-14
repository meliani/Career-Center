<?php

namespace App\Filament\App\Widgets;

use App\Models\Project;
use App\Models\Year;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class StudentProjectWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.student-project-widget';

    // Make widget full width for better presentation
    protected int | string | array $columnSpan = '1';

    public function getProject()
    {
        return Project::query()
            ->with(['timetable.room', 'timetable.timeslot', 'externalSupervisor']) // Eager load relationships
            ->whereHas('agreements', function (Builder $query) {
                $query->whereHas('agreeable', function (Builder $query) {
                    $query->where('student_id', auth()->id())
                        ->where('year_id', Year::current()->id);
                });
            })
            ->first();
    }
}
