<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Mail\ConnectingStudentsWithSupervisors;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;

class SendConnectingSupervisorsEmail extends BulkAction
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
                        ->send(new ConnectingStudentsWithSupervisors($record));
                });
            }
        });

        return $static;
    }
}
