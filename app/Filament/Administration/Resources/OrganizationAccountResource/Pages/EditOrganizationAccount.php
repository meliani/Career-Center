<?php

namespace App\Filament\Administration\Resources\OrganizationAccountResource\Pages;

use App\Filament\Administration\Resources\OrganizationAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationAccount extends EditRecord
{
    protected static string $resource = OrganizationAccountResource::class;

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
