<?php

namespace App\Filament\Administration\Resources\StudentResource\Pages;

use App\Filament\Administration\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
