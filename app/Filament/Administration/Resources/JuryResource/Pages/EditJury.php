<?php

namespace App\Filament\Administration\Resources\JuryResource\Pages;

use App\Filament\Administration\Resources\JuryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJury extends EditRecord
{
    protected static string $resource = JuryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
