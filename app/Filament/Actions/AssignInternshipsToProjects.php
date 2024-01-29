<?php

namespace App\Filament\Actions;

use App\Models\Internship;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\ScheduleParameters;
use App\Services\ScheduleService;

class AssignInternshipsToProjects extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    // public static function getDefaultName(): string
    // {
    //     return __('Schedule Head of Department');
    // }


    public static function make(?string $name = null): static
    {
            // dd('action called');
            // $static = app(static::class, [$name ?? static::getDefaultName()]);

        $static = app(static::class, [
            // dd('action called'),

            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, ScheduleParameters $record): void {
            // add a form to select the department
            // $record->withoutTimestamps(fn () => $record->assignDepartment($data['assigned_department']));
            ScheduleService::AssignInternshipsToProjects($record);
            // dd($record);

        });
        return $static;
    }
}
