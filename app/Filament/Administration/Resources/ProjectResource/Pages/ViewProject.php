<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Filament\Administration\Resources\ProjectResource;
use Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Filament\Actions\Action::make('edit page', 'edit')
                ->label('Switch to edit mode')
                ->icon('heroicon-o-pencil')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->tooltip('Edit project details and jury members')
                ->url(fn ($record) => \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$record->id]))
                ->hidden(fn () => ! (auth()->user()->isAdministrator() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead())),
        ];
    }
}
