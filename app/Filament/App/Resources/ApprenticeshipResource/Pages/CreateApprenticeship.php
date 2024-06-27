<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateApprenticeship extends CreateRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    // protected function getCreateFormAction(): Action
    // {
    //     return
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            // Action::make('create')
            //     ->label(__('Create'))
            //     ->icon('heroicon-o-plus-circle'),
            Action::make('create')
                ->label(__('Submit my information'))
                ->requiresConfirmation()
                ->modalHeading(__('Notice'))
                ->modalDescription(__('The residence will not be available after July 31st. Please make the necessary arrangements.'))
                ->modalSubmitActionLabel(__('I understand'))
                ->color('success')
                ->icon('heroicon-o-plus-circle')
                ->action(fn () => $this->create())
                ->keyBindings(['mod+s']),
        ];
    }
}
