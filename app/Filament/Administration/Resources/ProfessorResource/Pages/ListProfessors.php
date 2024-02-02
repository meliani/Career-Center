<?php

namespace App\Filament\Administration\Resources\ProfessorResource\Pages;

use App\Filament\Administration\Resources\ProfessorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfessors extends ListRecords
{
    protected static string $resource = ProfessorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
