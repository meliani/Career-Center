<?php

namespace App\Filament\Administration\Resources\OrganizationResource\Pages;

use App\Filament\Administration\Resources\OrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
