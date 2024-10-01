<?php

namespace App\Filament\Administration\Resources\OrganizationAccountResource\Pages;

use App\Filament\Administration\Resources\OrganizationAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationAccounts extends ListRecords
{
    protected static string $resource = OrganizationAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
