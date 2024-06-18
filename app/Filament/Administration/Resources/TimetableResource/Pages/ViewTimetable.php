<?php

namespace App\Filament\Administration\Resources\TimetableResource\Pages;

use App\Filament\Administration\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTimetable extends ViewRecord
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
