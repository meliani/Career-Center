<?php

namespace App\Filament\Administration\Widgets;

use App\Filament\Administration\Resources\InternshipResource;
use App\Models\InternshipAgreement;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AnouncementsCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 1;

    public Model | string | null $model = InternshipAgreement::class;

    // protected static string $view = 'filament.widgets.calendar-widget';

    public static function canView(): bool
    {
        // return auth()->user()->isSuperAdministrator();
        return false;
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
        return InternshipAgreement::where('created_at', '>=', $fetchInfo['start'])
            // ->where('ending_at', '<=', $fetchInfo['end'])
            ->get()
            // ->map(
            //     fn (InternshipAgreement $event) => EventData::make()
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
            ->map(function (InternshipAgreement $task) {
                return [
                    'id' => $task->id,
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
}
