<?php

namespace App\Filament\Actions\Action;

use App\Models\Project;
use Filament\Tables\Actions\Action;

class AuthorizeDefenseAction extends Action
{
    public static function getDefaultName(): string
    {
        return __('Authorize defense');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Project $record): void {

            $record->authorizeDefense();

        })
            ->requiresConfirmation();

        return $static;
    }
}
