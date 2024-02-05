<?php

namespace App\Filament\Administration\Resources\JuryResource\Pages;

use App\Filament\Administration\Resources\JuryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJuries extends ListRecords
{
    protected static string $resource = JuryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
