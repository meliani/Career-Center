<?php

namespace App\Filament\Administration\Resources\SentEmailResource\Pages;

use App\Filament\Administration\Resources\SentEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSentEmails extends ListRecords
{
    protected static string $resource = SentEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
