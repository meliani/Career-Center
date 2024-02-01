<?php

namespace App\Filament\Administration\Resources\JuryResource\Pages;

use App\Filament\Administration\Resources\JuryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJuries extends ManageRecords
{
    protected static string $resource = JuryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
