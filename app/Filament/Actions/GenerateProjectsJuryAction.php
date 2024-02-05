<?php

namespace App\Filament\Actions;

use App\Models\Internship;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\ScheduleParameters;
use App\Services\ProjectService;

class GenerateProjectsJuryAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, ScheduleParameters $record): void {
            ProjectService::GenerateProjectsJury($record);
        });
        return $static;
    }
}
