<?php

namespace App\Filament\Administration\Resources\DiplomaResource\Pages;

use App\Filament\Administration\Resources\DiplomaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiplomas extends ListRecords
{
    protected static string $resource = DiplomaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
