<?php

namespace App\Filament\Administration\Resources\DeliberationPVResource\Pages;

use App\Filament\Administration\Resources\DeliberationPVResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliberationPV extends EditRecord
{
    protected static string $resource = DeliberationPVResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
