<?php

namespace App\Filament\Actions;

use App\Models\InternshipAgreement;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\ScheduleParameters;
// use App\Services\TimeslotService;
use Filament\Notifications\Notification;
use App\Models\Timeslot;
use App\Services\AutoScheduleService;
use Carbon\CarbonPeriod;

class GenerateTimeslotsAction extends Action
{
    public $scheduleParameters;
       
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, ScheduleParameters $record): void {
            $scheduleParameters = $record;
            $AutoScheduleService = new AutoScheduleService($record);
            // dd($AutoScheduleService);

            // $workingDaysList = $AutoScheduleService->generateTimeslots($record);
            $workingDaysList = collect($AutoScheduleService->generateTimeslots($record));

            $workingDaysList->each(function ($day) use ($record) {
                    // dd($record);

                $day = collect($day);
                $day->each(function ($timeslot) use ($record) {

                    $timeslot = Carbon::parse($timeslot);
                    $timeslotPeriod = new Timeslot();
                    // $timeslotPeriod->start_time = $timeslot->start();
                    // $timeslotPeriod->end_time = $timeslot->end();
                    $timeslotPeriod->start_time = $timeslot;
                    $timeslotPeriod->end_time = $timeslot->addMinutes(90);
                    $timeslotPeriod->is_enabled = 1;
                    $timeslotPeriod->is_taken = 0;
                    // dd($record);
                    $timeslotPeriod->remaining_slots = $record->number_of_rooms;
                    $timeslotPeriod->save();
                });
            });
            new Notification('Timeslots generated successfully');
        });
        return $static;
    }
}
