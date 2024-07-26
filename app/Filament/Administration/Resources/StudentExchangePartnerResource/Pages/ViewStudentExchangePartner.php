<?php

namespace App\Filament\Administration\Resources\StudentExchangePartnerResource\Pages;

use App\Filament\Administration\Resources\StudentExchangePartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentExchangePartner extends ViewRecord
{
    protected static string $resource = StudentExchangePartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
