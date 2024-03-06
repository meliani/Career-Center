<?php

namespace App\Filament\Administration\Resources\SentEmailUrlClickedResource\Pages;

use App\Filament\Administration\Resources\SentEmailUrlClickedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSentEmailUrlClickeds extends ListRecords
{
    protected static string $resource = SentEmailUrlClickedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
