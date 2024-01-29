<?php

namespace App\Filament\Administration\Resources\ScheduleParametersResource\Pages;

use App\Filament\Administration\Resources\ScheduleParametersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduleParameters extends EditRecord
{
    protected static string $resource = ScheduleParametersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
