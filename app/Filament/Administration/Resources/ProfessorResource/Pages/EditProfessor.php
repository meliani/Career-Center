<?php

namespace App\Filament\Administration\Resources\ProfessorResource\Pages;

use App\Filament\Administration\Resources\ProfessorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProfessor extends EditRecord
{
    protected static string $resource = ProfessorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
