<?php

namespace App\Filament\Widgets;

use App\Filament\Administration\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Timetable;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class DefensesCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 20;

    public Model | string | null $model = Timetable::class;

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator();
    }

    public function getFormSchema(): array
    {
        return [
            // Forms\Components\TextInput::make('name'),

            Forms\Components\Grid::make()
                ->schema([
                    // Forms\Components\DateTimePicker::make('starts_at'),

                    // Forms\Components\DateTimePicker::make('ends_at'),
                ]),
        ];
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
        $events = Project::with('timetable.timeslot')
            ->get()
            ->map(
                fn (Project $project) => EventData::make()
                    ->id($project->id)
                    ->title($project->internship_agreements->map(fn ($agreement) => $agreement->id_pfe)->join(', ')
                    . ' - '
                    . $project->students->map(fn ($person) => $person->full_name)->join(', '))
                    ->start($project->timetable?->timeslot->start_time ?? '')
                    ->end($project->timetable?->timeslot->end_time ?? '')
                    ->url(
                        url: ProjectResource::getUrl(name: 'view', parameters: ['record' => $project]),
                        shouldOpenUrlInNewTab: true
                    )
            )
            ->toArray();

        return $events;
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Timetable $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->name,
                            'start' => $record->start,
                            'end' => $record->end,
                            'allDay' => $record->allDay,
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }
}