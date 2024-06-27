<?php

namespace App\Filament\App\Resources\InternshipOfferResource\Pages;

use App\Filament\App\Resources\InternshipOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternshipOffer extends EditRecord
{
    protected static string $resource = InternshipOfferResource::class;

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
