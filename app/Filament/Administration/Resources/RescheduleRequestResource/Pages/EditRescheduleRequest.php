<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Pages;

use App\Filament\Administration\Resources\RescheduleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRescheduleRequest extends EditRecord
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
