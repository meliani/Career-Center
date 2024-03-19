<?php

namespace App\Filament\Actions\BulkAction\Email;

use Filament\Tables\Actions\BulkAction;

class SendProfessorsProjectsOverview extends BulkAction
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function ($records): void {
            foreach ($records as $professor) {
                $professor->notify(new \App\Notifications\ProfessorsProjectsOverview());
            }

            // send copy to administrators as well
            $admin = \App\Models\User::administrators();
            foreach ($admin as $administrator) {

                $administrator->notify(new \App\Notifications\ProfessorsProjectsOverview());
            }

        });

        return $static;
    }
}
