<?php

namespace App\Filament\App\Resources\InternshipOfferResource\Pages;

use App\Filament\App\Resources\InternshipOfferResource;
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
