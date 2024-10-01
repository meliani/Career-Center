<?php

namespace App\Filament\Administration\Resources\MidweekEventSessionResource\Pages;

use App\Filament\Administration\Resources\MidweekEventSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMidweekEventSession extends ViewRecord
{
    protected static string $resource = MidweekEventSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
