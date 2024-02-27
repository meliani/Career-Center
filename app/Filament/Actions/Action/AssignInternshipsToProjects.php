<?php

namespace App\Filament\Actions\Action;

use App\Services\ProjectService;
use Filament\Tables\Actions\Action;

class AssignInternshipsToProjects extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (): void {
            ProjectService::AssignInternshipsToProjects();
        });

        return $static;
    }
}
