<?php

namespace App\Filament\Widgets;

use App\Models\MidweekEvent;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class MidweekEventsCalendarWidget extends FullCalendarWidget
{
    // protected static ?int $sort = 20;

    protected static bool $isLazy = false;

    public Model | string | null $model = MidweekEvent::class;

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

    public function config(): array
    {
        return [
            'headerToolbar' => [
                'start' => 'dayGridMonth,timeGridWeek',
                'center' => 'title',
                'end' => 'today prev,next',
            ],
            'initialView' => 'dayGridMonth',
        ];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // we will get events from MidweekEvent model

        $events = MidweekEvent::with('midweekEventSession')->get()
            ->map(function ($event) {
                return EventData::make()
                    ->id($event->id)
                    ->title($event->name)
                    ->start($event->midweekEventSession->session_start_at)
                    ->end($event->midweekEventSession->session_end_at)
                    ->allDay(false)
                    ->url('#');
            });

        return $events->toArray();

    }
}
