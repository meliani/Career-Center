<?php

namespace App\Filament\Administration\Resources\FinalProjectResource\Pages;

use App\Filament\Administration\Resources\FinalProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinalProjects extends ListRecords
{
    protected static string $resource = FinalProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
