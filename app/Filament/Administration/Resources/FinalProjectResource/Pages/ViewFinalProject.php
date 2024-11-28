<?php

namespace App\Filament\Administration\Resources\FinalProjectResource\Pages;

use App\Filament\Administration\Resources\FinalProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinalProject extends ViewRecord
{
    protected static string $resource = FinalProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
