<?php

namespace App\Filament\Actions;

use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\InternshipAgreement;
use App\Services\ProjectService;

class CreateProjectFromInternshipAgreement extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (InternshipAgreement $record): void {
            ProjectService::CreateFromInternshipAgreement($record);
        });
        return $static;
    }
}
