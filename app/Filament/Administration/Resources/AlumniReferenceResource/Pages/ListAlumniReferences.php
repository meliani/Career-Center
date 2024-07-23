<?php

namespace App\Filament\Administration\Resources\AlumniReferenceResource\Pages;

use App\Filament\Administration\Resources\AlumniReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlumniReferences extends ListRecords
{
    protected static string $resource = AlumniReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
