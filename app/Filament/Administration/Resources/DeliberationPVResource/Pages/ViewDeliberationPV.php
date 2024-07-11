<?php

namespace App\Filament\Administration\Resources\DeliberationPVResource\Pages;

use App\Filament\Administration\Resources\DeliberationPVResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliberationPV extends ViewRecord
{
    protected static string $resource = DeliberationPVResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
