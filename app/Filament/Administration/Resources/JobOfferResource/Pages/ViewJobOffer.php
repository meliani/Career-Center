<?php

namespace App\Filament\Administration\Resources\JobOfferResource\Pages;

use App\Filament\Administration\Resources\JobOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJobOffer extends ViewRecord
{
    protected static string $resource = JobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
