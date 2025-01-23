<?php

namespace App\Filament\Actions\Action;

use App\Services\FinalProjectService;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\Action;

class AssignFinalInternshipsToProjects extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()
            ->requiresConfirmation()
            ->form([
                Checkbox::make('override_existing')
                    ->label(__('Override existing projects'))
                    ->helperText(__('If checked, existing projects will be updated with new data'))
                    ->default(false),
            ])
            ->label(__('Migrate to Projects'))
            ->modalHeading(__('Migrate Final Year Internships to Projects'))
            ->modalDescription(__('This action will create projects from signed internship agreements.'))
            ->successNotificationTitle(__('Projects created successfully'))
            ->color('success')
            ->icon('heroicon-o-arrow-path')
            ->action(function (array $data): void {
                FinalProjectService::AssignFinalInternshipsToProjects($data['override_existing'] ?? false);
            });

        return $static;
    }
}
