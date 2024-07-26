<?php

namespace App\Filament\Administration\Resources\StudentExchangePartnerResource\Pages;

use App\Filament\Administration\Resources\StudentExchangePartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentExchangePartners extends ListRecords
{
    protected static string $resource = StudentExchangePartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
