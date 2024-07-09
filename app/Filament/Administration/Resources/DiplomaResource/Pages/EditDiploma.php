<?php

namespace App\Filament\Administration\Resources\DiplomaResource\Pages;

use App\Filament\Administration\Resources\DiplomaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiploma extends EditRecord
{
    protected static string $resource = DiplomaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
