<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\Pages;

use App\Filament\Administration\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprenticeships extends ListRecords
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
