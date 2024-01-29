<?php

namespace App\Filament\Administration\Resources\ScheduleParametersResource\Pages;

use App\Filament\Administration\Resources\ScheduleParametersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScheduleParameters extends ListRecords
{
    protected static string $resource = ScheduleParametersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
