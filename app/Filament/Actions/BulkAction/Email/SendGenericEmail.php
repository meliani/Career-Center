<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Mail\GenericEmail;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;

class SendGenericEmail extends BulkAction
{
    public $emailSubject;

    public $emailBody;

    public static function make(?string $name = null, string $emailSubject = '', string $emailBody = ''): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
            'emailSubject' => $emailSubject ?? '',
            'emailBody' => $emailBody ?? '',
        ]);

        $static->emailSubject = $emailSubject;
        $static->emailBody = $emailBody;

        $static->configure()->form([
            TextInput::make('emailSubject')->required(),
            RichEditor::make('emailBody'),
        ])
            ->action(function (array $data, $records): void {
                foreach ($records as $record) {
                    if (method_exists($record, 'students')) {
                        foreach ($record->students as $student) {
                            if ($student->email_perso) {
                                dispatch(function () use ($student, $data) {
                                    Mail::to([$student->student->email_perso, $student->student->email])
                                        ->send(new GenericEmail($student, $data['emailSubject'], $data['emailBody']));
                                });
                            }
                        }
                    } elseif (method_exists($record, 'student')) {
                        if ($record->student->email_perso) {
                            dispatch(function () use ($record, $data) {
                                Mail::to([$record->student->email_perso, $record->student->email])
                                    ->send(new GenericEmail($record->student, $data['emailSubject'], $data['emailBody']));
                            });
                        }
                    } elseif (class_basename($record) === 'Student') {
                        // $classname = get_class($record);
                        // dd($classname, class_basename($record));
                        if ($record->email_perso) {
                            dispatch(function () use ($record, $data) {
                                Mail::to([$record->student->email_perso, $record->student->email])
                                    ->send(new GenericEmail($record, $data['emailSubject'], $data['emailBody']));
                            });
                        }
                    }
                }
            });

        return $static;
    }
}
