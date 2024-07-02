<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\Pages;

use App\Filament\Administration\Resources\ApprenticeshipResource;
use Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewApprenticeship extends ViewRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Filament\Actions\Action::make('edit page', 'edit')
                ->label(__('Switch to edit mode'))
                ->icon('heroicon-o-pencil')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->tooltip('Edit project details and jury members')
                ->url(fn ($record) => \App\Filament\Administration\Resources\ApprenticeshipResource::getUrl('edit', [$record->id]))
                ->hidden(fn () => ! (auth()->user()->isAdministrator() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead())),

        ];
    }
}
