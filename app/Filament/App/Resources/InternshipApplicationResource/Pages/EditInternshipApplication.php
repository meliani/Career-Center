<?php

namespace App\Filament\App\Resources\InternshipApplicationResource\Pages;

use App\Filament\App\Resources\InternshipApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternshipApplication extends EditRecord
{
    protected static string $resource = InternshipApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
