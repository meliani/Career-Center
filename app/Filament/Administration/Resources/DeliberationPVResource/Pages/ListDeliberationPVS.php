<?php

namespace App\Filament\Administration\Resources\DeliberationPVResource\Pages;

use App\Filament\Administration\Resources\DeliberationPVResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliberationPVS extends ListRecords
{
    protected static string $resource = DeliberationPVResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
