<?php

namespace App\Filament\Research\Resources\MasterResearchInternshipAgreementResource\Pages;

use App\Filament\Research\Resources\MasterResearchInternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMasterResearchInternshipAgreement extends ViewRecord
{
    protected static string $resource = MasterResearchInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
