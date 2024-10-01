<?php

namespace App\Filament\Administration\Resources\MidweekEventResource\Pages;

use App\Filament\Administration\Resources\MidweekEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMidweekEvents extends ListRecords
{
    protected static string $resource = MidweekEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
