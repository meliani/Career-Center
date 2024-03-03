<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Mail\ConnectingStudentsWithSupervisors;
use App\Models\Student;
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
                // dd($record->students->first()->email_perso);
                $record->students->each(function (Student $student) use ($record) {
                    $student->notify(new \App\Notifications\ProjectSupervisorAdded($record, $student));
                });
                // dispatch(function () use ($record) {
                //     // dd($record);
                //     Mail::to($record->students->first()->email_perso)
                //         ->send(new ConnectingStudentsWithSupervisors($record));
                // });
            }
        });

        return $static;
    }
}
