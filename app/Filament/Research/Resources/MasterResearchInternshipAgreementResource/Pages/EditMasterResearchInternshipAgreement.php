<?php

namespace App\Filament\Research\Resources\MasterResearchInternshipAgreementResource\Pages;

use App\Filament\Research\Resources\MasterResearchInternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterResearchInternshipAgreement extends EditRecord
{
    protected static string $resource = MasterResearchInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
