<?php

namespace App\Filament\Org\Resources\InternshipOfferResource\Pages;

use App\Filament\Org\Resources\InternshipOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInternshipOffer extends ViewRecord
{
    protected static string $resource = InternshipOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
