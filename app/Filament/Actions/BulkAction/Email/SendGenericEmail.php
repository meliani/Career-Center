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
                    if ($record->email_perso) {
                        dispatch(function () use ($record, $data) {
                            Mail::to($record->email_perso)
                                ->send(new GenericEmail($record, $data['emailSubject'], $data['emailBody']));
                        });

                    }
                }
            });

        return $static;
    }
}
