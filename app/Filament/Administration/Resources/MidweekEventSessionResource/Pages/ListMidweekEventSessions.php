<?php

namespace App\Filament\Administration\Resources\MidweekEventSessionResource\Pages;

use App\Filament\Administration\Resources\MidweekEventSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMidweekEventSessions extends ListRecords
{
    protected static string $resource = MidweekEventSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
