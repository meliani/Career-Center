<?php

namespace App\Filament\Administration\Resources\YearlyTimelineResource\Pages;

use App\Filament\Administration\Resources\YearlyTimelineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateYearlyTimeline extends CreateRecord
{
    protected static string $resource = YearlyTimelineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
