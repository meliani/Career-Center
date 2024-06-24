<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\Pages;

use App\Filament\Administration\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprenticeship extends EditRecord
{
    protected static string $resource = ApprenticeshipResource::class;

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
