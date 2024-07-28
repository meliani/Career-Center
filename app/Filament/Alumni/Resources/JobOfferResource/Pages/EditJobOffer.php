<?php

namespace App\Filament\Alumni\Resources\JobOfferResource\Pages;

use App\Filament\Alumni\Resources\JobOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobOffer extends EditRecord
{
    protected static string $resource = JobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
