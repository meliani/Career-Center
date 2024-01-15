<?php

namespace App\Filament\ProgramCoordinator\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Filament\Actions\Action;
use App\Models\Internship;
use App\Filament\ProgramCoordinator\Resources\InternshipResource;
use Saade\FilamentFullCalendar\Data\EventData;

class AnouncementsCalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Internship::class;

    // protected static string $view = 'filament.widgets.calendar-widget';

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // You can use $fetchInfo to filter events by date.
        // This method should return an array of event-like objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#returning-events
        // You can also return an array of EventData objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#the-eventdata-class
        return Internship::
        whereHas('student', function ($q) {
            $q->where('filiere_text', 'like', '%AMOA%');
        })            ->where('created_at', '>=', $fetchInfo['start'])
            // ->where('ending_at', '<=', $fetchInfo['end'])
            ->get()
            // ->map(
            //     fn (Internship $event) => EventData::make()
            //         ->id($event->id)
            //         ->title($event->title)
            //         ->start($event->stating_at)
            //         ->end($event->ending_at)
            //         ->url(
            //             url: InternshipResource::getUrl(name: 'view', parameters: ['record' => $event]),
            //             shouldOpenUrlInNewTab: true
            //         )
            // )
            // ->all();
            ->map(function (Internship $task) {
                return [
                    'id'    => $task->id,
                    'title' => $task->student?->full_name,
                    'start' => $task?->created_at,
                    // 'end'   => $task->updated_at,
                ];
            })
            ->toArray();
    }
    protected function headerActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function viewAction(): Action
    {
        return Actions\ViewAction::make();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('student.full_name'),
            Forms\Components\TextInput::make('title'),

            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\DateTimePicker::make('created_at'),
                    Forms\Components\DateTimePicker::make('updated_at'),
                ]),
        ];
    }
    public static function canView(): bool
{
    return false;
}
}
