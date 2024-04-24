<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApprenticeship extends ViewRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
