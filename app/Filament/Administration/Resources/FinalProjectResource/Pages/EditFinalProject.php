<?php

namespace App\Filament\Administration\Resources\FinalProjectResource\Pages;

use App\Filament\Administration\Resources\FinalProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinalProject extends EditRecord
{
    protected static string $resource = FinalProjectResource::class;

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
