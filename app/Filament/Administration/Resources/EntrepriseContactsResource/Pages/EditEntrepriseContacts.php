<?php

namespace App\Filament\Administration\Resources\EntrepriseContactsResource\Pages;

use App\Filament\Administration\Resources\EntrepriseContactsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntrepriseContacts extends EditRecord
{
    protected static string $resource = EntrepriseContactsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
