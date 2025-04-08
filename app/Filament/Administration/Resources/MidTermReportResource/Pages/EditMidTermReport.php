<?php

namespace App\Filament\Administration\Resources\MidTermReportResource\Pages;

use App\Filament\Administration\Resources\MidTermReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMidTermReport extends EditRecord
{
    protected static string $resource = MidTermReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
