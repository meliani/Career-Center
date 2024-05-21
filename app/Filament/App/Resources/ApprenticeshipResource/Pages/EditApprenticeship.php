<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprenticeship extends EditRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
        ];
    }

    // protected function getFormActions(): array
    // {
    //     return [];
    // }
}
