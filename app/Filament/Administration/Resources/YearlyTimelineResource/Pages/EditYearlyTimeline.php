<?php

namespace App\Filament\Administration\Resources\YearlyTimelineResource\Pages;

use App\Filament\Administration\Resources\YearlyTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditYearlyTimeline extends EditRecord
{
    protected static string $resource = YearlyTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
