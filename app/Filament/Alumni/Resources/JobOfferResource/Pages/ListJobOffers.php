<?php

namespace App\Filament\Alumni\Resources\JobOfferResource\Pages;

use App\Filament\Alumni\Resources\JobOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobOffers extends ListRecords
{
    protected static string $resource = JobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
