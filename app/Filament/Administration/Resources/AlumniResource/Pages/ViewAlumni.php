<?php

namespace App\Filament\Administration\Resources\AlumniResource\Pages;

use App\Filament\Administration\Resources\AlumniResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlumni extends ViewRecord
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
