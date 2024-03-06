<?php

namespace App\Filament\Administration\Resources\SentEmailUrlClickedResource\Pages;

use App\Filament\Administration\Resources\SentEmailUrlClickedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSentEmailUrlClicked extends EditRecord
{
    protected static string $resource = SentEmailUrlClickedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
