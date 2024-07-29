<?php

namespace App\Filament\Administration\Resources\AlumniReferenceResource\Pages;

use App\Filament\Administration\Resources\AlumniReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlumniReference extends EditRecord
{
    protected static string $resource = AlumniReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
