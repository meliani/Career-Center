<?php

namespace App\Filament\Administration\Resources\OrganizationAccountResource\Pages;

use App\Filament\Administration\Resources\OrganizationAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganizationAccount extends ViewRecord
{
    protected static string $resource = OrganizationAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
