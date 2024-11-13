<?php

namespace App\Filament\Administration\Resources\YearlyTimelineResource\Pages;

use App\Filament\Administration\Resources\YearlyTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListYearlyTimelines extends ListRecords
{
    protected static string $resource = YearlyTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
