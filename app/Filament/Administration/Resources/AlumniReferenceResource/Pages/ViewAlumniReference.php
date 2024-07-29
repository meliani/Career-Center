<?php

namespace App\Filament\Administration\Resources\AlumniReferenceResource\Pages;

use App\Filament\Administration\Resources\AlumniReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlumniReference extends ViewRecord
{
    protected static string $resource = AlumniReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
