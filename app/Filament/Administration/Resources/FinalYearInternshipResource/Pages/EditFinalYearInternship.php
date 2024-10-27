<?php

namespace App\Filament\Administration\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Filament\Administration\Resources\FinalYearInternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinalYearInternship extends EditRecord
{
    protected static string $resource = FinalYearInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
