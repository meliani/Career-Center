<?php

namespace App\Filament\Administration\Resources\DiplomaResource\Pages;

use App\Filament\Administration\Resources\DiplomaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiploma extends ViewRecord
{
    protected static string $resource = DiplomaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
