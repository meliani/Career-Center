<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Mail\GenericEmail;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;

class SendGenericEmail extends BulkAction
{
    public $emailSubject;

    public $emailBody;

    public static bool $success = false;

    public static int $emailCount = 0;

    // public function __construct(?string $name)
    // {
    //     parent::__construct($name);
    //     $this->success = true;
    // }

    private static function getSuccess(): bool
    {
        return self::$success;
    }

    private static function setSuccess(bool $success): void
    {
        self::$success = $success;
    }

    private static function getEmailCount(): int
    {
        return self::$emailCount;
    }

    private static function setEmailCount(int $emailCount): void
    {
        self::$emailCount = $emailCount;
    }

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
                            if ($student->email_perso || $student->email) {
                                dispatch(function () use ($student, $data) {
                                    Mail::to([$student?->email_perso, $student?->email])
                                        ->send(new GenericEmail($student, $data['emailSubject'], $data['emailBody']));
                                });
                                self::setSuccess(true);
                                self::setEmailCount(self::getEmailCount() + 1);
                            }
                        }
                    } elseif (method_exists($record, 'student')) {
                        if ($record->student->email_perso || $record->student->email) {
                            dispatch(function () use ($record, $data) {
                                Mail::to([$record->student?->email_perso, $record->student?->email])
                                    ->send(new GenericEmail($record->student, $data['emailSubject'], $data['emailBody']));
                            });
                            self::setSuccess(true);
                            self::setEmailCount(self::getEmailCount() + 1);
                        }
                    } elseif (class_basename($record) === 'Student') {
                        // $classname = get_class($record);
                        // dd($classname, class_basename($record));
                        if ($record->email_perso || $record->email) {
                            dispatch(function () use ($record, $data) {
                                Mail::to([$record?->email_perso, $record?->email])
                                    ->send(new GenericEmail($record, $data['emailSubject'], $data['emailBody']));
                            });
                            self::setSuccess(true);
                            self::setEmailCount(self::getEmailCount() + 1);
                        }
                    } else {
                        self::setSuccess(false);
                    }
                }
                Notification::make()
                    ->title(self::getSuccess() ? self::getEmailCount() . ' ' . __('Emails sent successfully') : __('Emails not sent, there was an error'))
                    ->send();
                // dd($data, $records, self::getSuccess());
            });

        return $static;
    }
}
