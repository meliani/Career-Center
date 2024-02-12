<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\InternshipAgreement;
use App\Services\ProjectService;

class ImportProfessorsFromInternshipAgreements extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (): void {
            ProjectService::ImportProfessorsFromInternshipAgreements();
        });
        return $static;
    }
}
