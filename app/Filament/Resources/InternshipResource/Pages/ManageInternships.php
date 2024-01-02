<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Internship;
use Filament\Actions\Action;
class ManageInternships extends ManageRecords
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Action::make('review')
            // ->action(fn (Internship $record) => $record->review())
            // ->requiresConfirmation(),
        ];
    }
}
