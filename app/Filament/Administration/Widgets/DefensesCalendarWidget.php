<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Administration\Resources\ProjectResource;
use App\Filament\Administration\Resources\TimetableResource;
use App\Models\Project;
use App\Models\Timetable;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class DefensesCalendarWidget extends FullCalendarWidget
{
    // protected static ?int $sort = 20;

    protected static bool $isLazy = false;

    public Model | string | null $model = Timetable::class;

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();

    }

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isAdministrativeSupervisor();
    }

    // public function getFormSchema(): array
    // {
    //     return [
    //         // Forms\Components\TextInput::make('name'),

    //         Forms\Components\Grid::make()
    //             ->schema([
    //                 // Forms\Components\DateTimePicker::make('starts_at'),

    //                 // Forms\Components\DateTimePicker::make('ends_at'),
    //             ]),
    //     ];
    // }
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // You can use $fetchInfo to filter events by date.
        // This method should return an array of event-like objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#returning-events
        // You can also return an array of EventData objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#the-eventdata-class

        // we want to get data from project->timetable->timeslot start_at and end_at

        // $events = Project::with('timetable.timeslot')
        //     ->get()
        //     ->map(function (Project $project) {
        //         $personsLastNames = $project->students->map(fn ($person) => $person->last_name)->join(', ');

        //         return [
        //             'id' => $project->id,
        //             'title' => $personsLastNames,
        //             'start' => $project->timetable?->timeslot->start_time,
        //             'end' => $project->timetable?->timeslot->end_time,
        //         ];
        //     })
        //     ->toArray();
        $events = Project::withoutGlobalScopes()
            ->whereHas('timetable')
            ->withoutGlobalScopes()
            ->with('timetable.timeslot', 'agreements.agreeable')
            ->withoutGlobalScopes()
            ->get()
            ->map(
                fn (Project $project) => EventData::make()
                    ->id($project->id)
                    ->title(
                        $project->getStudentsCollection()->map(fn ($person) => $person->full_name)->join(' & ') .
                    "\n\r" . ', ' .
                    $project->timetable->room?->name . ',' .
                    ' ID PFE: ' .
                    $project->id_pfe
                    )
                    ->start($project->timetable->timeslot->start_time ?? '')
                    ->end($project->timetable->timeslot->end_time ?? '')
                    // ->url(
                    //     url: ProjectResource::getUrl(name: 'view', parameters: ['record' => $project]),
                    //     shouldOpenUrlInNewTab: true
                    // )
                    // ->url(
                    //     url: TimetableResource::getUrl(name: 'edit', parameters: ['record' => $project->timetable]),
                    //     shouldOpenUrlInNewTab: true
                    // )
                    ->url(
                        //                 url: auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()
                        // ? TimetableResource::getUrl(name: 'edit', parameters: ['record' => $project->timetable])
                        // : ProjectResource::getUrl(name: 'view', parameters: ['record' => $project]),
                        // url: ProjectResource::getUrl(name: 'view', parameters: ['record' => $project]),
                        url: auth()->user()->can('update', $project) ? ProjectResource::getUrl(name: 'view', parameters: ['record' => $project]) : '#',
                        shouldOpenUrlInNewTab: false
                    )
                    ->extendedProps([
                        'description' => $project->title,
                        // 'professors' => $project->timetable->professors->map(fn ($professor) => $professor->full_name)->join(', '),
                        'room' => $project->timetable?->room?->name,
                    ])
                    // ->backgroundColor($project->timetable->room->color)
                    // ->textColor($project->timetable->room->color)
                    ->resourceId($project->timetable?->room ? $project->timetable->room->id : 'No room assigned')
                    ->allDay(false)
            )
            ->toArray();

        // dd($events);

        return $events;
    }
}
