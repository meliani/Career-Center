<?php

namespace App\Filament\Actions;

use App\Mail\JoinPlatformInvitation;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;

class SendBulkInvitationEmail extends BulkAction
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function ($records): void {

            foreach ($records as $record) {
                dispatch(function () use ($record) {
                    Mail::to($record->email)
                        ->send(new JoinPlatformInvitation($record));
                });
            }
        });

        return $static;
    }
}
