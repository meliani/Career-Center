<?php

namespace App\Filament\App\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Filament\App\Resources\FinalYearInternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinalYearInternshipAgreement extends ViewRecord
{
    protected static string $resource = FinalYearInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
