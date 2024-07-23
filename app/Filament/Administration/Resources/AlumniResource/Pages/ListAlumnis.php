<?php

namespace App\Filament\Administration\Resources\AlumniResource\Pages;

use App\Filament\Administration\Resources\AlumniResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlumnis extends ListRecords
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
