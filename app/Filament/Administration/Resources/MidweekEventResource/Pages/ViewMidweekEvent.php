<?php

namespace App\Filament\Administration\Resources\MidweekEventResource\Pages;

use App\Filament\Administration\Resources\MidweekEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMidweekEvent extends ViewRecord
{
    protected static string $resource = MidweekEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
