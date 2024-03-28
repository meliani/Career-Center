<?php

namespace App\Filament\Administration\Resources\TimeslotResource\Pages;

use App\Filament\Administration\Resources\TimeslotResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTimeslots extends ManageRecords
{
    protected static string $resource = TimeslotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
