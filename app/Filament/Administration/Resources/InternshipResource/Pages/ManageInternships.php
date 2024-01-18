<?php

namespace App\Filament\Administration\Resources\InternshipResource\Pages;

use App\Filament\Administration\Resources\InternshipResource;
use Filament\Actions\Action;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ManageInternships extends ManageRecords
{

    use HasToggleableTable;
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Action::make('cards view')
            // ->url(route('card-view')),
        ];
    }
}
