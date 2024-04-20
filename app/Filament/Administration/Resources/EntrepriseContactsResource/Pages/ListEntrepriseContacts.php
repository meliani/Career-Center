<?php

namespace App\Filament\Administration\Resources\EntrepriseContactsResource\Pages;

use App\Filament\Administration\Resources\EntrepriseContactsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntrepriseContacts extends ListRecords
{
    protected static string $resource = EntrepriseContactsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
