<?php

namespace App\Filament\Org\Resources\InternshipOfferResource\Pages;

use App\Filament\Org\Resources\InternshipOfferResource;
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
