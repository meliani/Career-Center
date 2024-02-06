<?php

namespace App\Filament\Administration\Resources\InternshipAgreementResource\Pages;

use App\Filament\Administration\Resources\InternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternshipAgreement extends EditRecord
{
    protected static string $resource = InternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
