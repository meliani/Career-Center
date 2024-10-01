<?php

namespace App\Filament\Administration\Resources\MidweekEventResource\Pages;

use App\Filament\Administration\Resources\MidweekEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMidweekEvent extends EditRecord
{
    protected static string $resource = MidweekEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
