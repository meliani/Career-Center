<?php

namespace App\Filament\Administration\Resources\StudentExchangePartnerResource\Pages;

use App\Filament\Administration\Resources\StudentExchangePartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentExchangePartner extends EditRecord
{
    protected static string $resource = StudentExchangePartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
