<?php

namespace App\Filament\Administration\Resources\DocumentTemplateResource\Pages;

use App\Filament\Administration\Resources\DocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTemplates extends ListRecords
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
