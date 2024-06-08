<?php

namespace App\Filament\Administration\Resources\InternshipOfferResource\Pages;

use App\Filament\Administration\Resources\InternshipOfferResource;
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
