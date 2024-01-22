<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentMail;
use Illuminate\Support\Collection;
use App\Models\Internship;
use Illuminate\Support\Carbon;
use App\Models\Student;
use Filament\Forms\Components;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class SendBulkEmail extends BulkAction
{
    public $emailSubject;
    public $emailBody;

    // public function form(array | Closure | null $form): static
    // {
    //     // $this->form = $form;
    //     return $this->schema([
    //         Components\TextInput::make('emailSubject')
    //             ->required()
    //             ->label('Email Subject'),

    //         Components\Textarea::make('emailBody')
    //             ->required()
    //             ->label('Email Body'),
    //     ]);
    // }

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
            ->action(function (array $data,$records) use ($emailSubject, $emailBody): void {

                // dd emailSubject and emailBody from the form cause dd($emailSubject) doesn't work
                // $static = $this;
                // dd($emailSubject, $emailBody);

                foreach ($records as $record) {
                    if ($record->email_perso) {
                        dispatch(function () use ($record, $emailSubject, $emailBody) {
                            Mail::to($record->email_perso)
                                ->send(new StudentMail($record, $emailSubject, $emailBody));
                        });
                        // Mail::to($record->email_perso)
                        //     // ->send(new StudentMail($record, $emailSubject, $emailBody));
                        //     // ->send(new StudentMail(
                        //     //     $record,
                        //     //     $data['emailSubject'],
                        //     //     $data['emailBody'],
                        //     // ));
                            
                            // ->queue(new StudentMail(
                            //     $record,
                            //     $data['emailSubject'],
                            //     $data['emailBody'],
                            // ));
                            
                    }
                }
            });

        // Tables\Actions\Action::make('Mark as Signed')
        // ->action(fn (Internship $internship) => $internship->sign_off())
        // ->requiresConfirmation(
        //     fn (Internship $internship) => "Are you sure you want to mark this internship as Signed?"),
        // Tables\Actions\Action::make('sendEmail')
        // ->form([
        //     TextInput::make('subject')->required(),
        //     RichEditor::make('body')->required(),
        // ])
        // ->action(fn (array $data, Internship $internship) => Mail::to($internship->student->email_perso)
        //     ->send(new DefenseReadyEmail(
        //         $data['subject'],
        //         $data['body'],
        //     ))
        // )
        // \App\Filament\Resources\InternshipResource\Actions\ValidateAction::make()
        // ->action(fn (Internship $internship) => $internship->validate()),
        return $static;
    }
    // public function handle($selectedRowsQuery)
    // {
    //     // dd($this->emailSubject); // Dump the emailSubject property

    //     $selectedRowsQuery->each(function ($student) {
    //         Mail::to($student->email)->send(new StudentMail($student, $this->emailSubject, $this->emailBody));
    //     });
    // }
}
