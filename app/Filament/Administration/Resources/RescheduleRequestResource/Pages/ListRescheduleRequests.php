<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Pages;

use App\Filament\Administration\Resources\RescheduleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRescheduleRequests extends ListRecords
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
