<?php

namespace App\Filament\Administration\Resources\MidTermReportResource\Pages;

use App\Filament\Administration\Resources\MidTermReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMidTermReports extends ListRecords
{
    protected static string $resource = MidTermReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
