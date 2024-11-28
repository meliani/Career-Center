<?php

namespace App\Filament\Actions\Action;

use App\Services\FinalProjectService;
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
            ->label('Migrate to Projects')
            ->modalHeading('Migrate Final Year Internships to Projects')
            ->modalDescription('This action will create projects from signed internship agreements.')
            ->successNotificationTitle('Projects created successfully')
            ->color('success')
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
                FinalProjectService::AssignFinalInternshipsToProjects();
            });

        return $static;
    }
}
