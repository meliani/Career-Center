<?php

namespace App\Filament\Administration\Resources\YearlyTimelineResource\Pages;

use App\Filament\Administration\Resources\YearlyTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewYearlyTimeline extends ViewRecord
{
    protected static string $resource = YearlyTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
