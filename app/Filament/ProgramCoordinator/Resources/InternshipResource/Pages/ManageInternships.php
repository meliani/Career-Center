<?php

namespace App\Filament\ProgramCoordinator\Resources\InternshipResource\Pages;

use App\Filament\ProgramCoordinator\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInternships extends ManageRecords
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
