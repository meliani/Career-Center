<?php

namespace App\Filament\Administration\Resources\OfferResource\Pages;

use App\Filament\Administration\Resources\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOffers extends ManageRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
