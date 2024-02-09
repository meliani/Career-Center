<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericEmail;
use Illuminate\Support\Collection;
use App\Models\InternshipAgreement;
use Illuminate\Support\Carbon;
use App\Models\Student;
use Filament\Forms\Components;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class SendUsersBulkEmail extends BulkAction
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
        ->action(function (array $data,$records): void {

            foreach ($records as $record) {
                // if ($record->email) {
                    dispatch(function () use ($record, $data) {
                        Mail::to($record->email)
                            ->send(new GenericEmail($record, $data['emailSubject'], $data['emailBody']));
                    });
                // }
            }
        });
        return $static;
    }
}
