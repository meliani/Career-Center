<?php

namespace App\Filament\Administration\Resources\TimetableResource\Pages;

use App\Filament\Administration\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimetable extends EditRecord
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
