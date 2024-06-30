<?php

namespace App\Filament\Actions\Action;

use App\Models\Project;
use App\Notifications\DefenseAuthorizedNotification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;

class SendDefenseEmailAction extends Action
{
    public static array $emails = [];

    protected static $emailBody;

    public static function getDefaultName(): string
    {
        return __('Send defense email');
    }

    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Project $record): void {
            event(new \App\Events\DefenseAuthorized($record, $data['emails']));
            // dd($notification->toMail(auth()->user()->email)->render());

        })
            // ->fillForm(function ($record) {
            //     $notification = new DefenseAuthorizedNotification($record);

            //     self::$emailBody = $notification->toMail(auth()->user()->email)->render();
            //     // dd(self::$emailBody);

            //     return [
            //         'email_body' => fn () => new HtmlString(self::$emailBody),
            //         // 'email_body' => self::$emailBody,
            //         // 'emails' => self::getEmails($record),
            //     ];
            // })
            ->steps(function ($record) {
                $notification = new DefenseAuthorizedNotification($record);

                self::$emailBody = $notification->toMail(auth()->user()->email)->render();

                return [
                    // Step::make('Evaluation Sheet')
                    //     // ->label(__('Evaluation Sheet'))
                    //     ->description(__('View and download the evaluation sheet'))
                    //     ->schema([
                    //         PdfViewerField::make('evaluation_sheet_url')
                    //             ->label('Evaluation Sheet')
                    //             // ->minHeight('40svh')
                    //             ->fileUrl($record->evaluation_sheet_url),
                    //     ]),
                    Step::make('Preview')
                        ->label(__('Preview'))
                        ->description(__('Preview email'))
                        ->schema([
                            // \Filament\Forms\Components\RichEditor::make('Preview1')
                            //     ->columnSpanFull()
                            //     ->default(new HtmlString(self::$emailBody)),
                            \Filament\Forms\Components\Placeholder::make('Preview')->hiddenLabel()
                                ->content(self::$emailBody),
                        ]),

                    Step::make('Recipients')
                        ->label(__('Recipients'))
                        ->description(__('View and edit recipients'))
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('Attachments')
                                ->content(new HtmlString('<a href="' . $record->evaluation_sheet_url . '" class="text-blue-500 hover:underline">Evaluation Sheet for ' . $record->id_pfe . '</a>')),
                            \Filament\Forms\Components\TagsInput::make('emails')
                                ->label('Emails')
                                ->splitKeys(['Tab', ',', ';', ' '])
                                ->nestedRecursiveRules([
                                    'email',
                                ])
                                ->placeholder(__('Enter emails separated by commas'))
                                ->default(function () use ($record) {
                                    // Assuming getEmails is a method that takes the $record and returns an array of emails
                                    return $record ? self::getEmails($record) : [];
                                }),
                        ]),

                ];
            })
            ->color('success');
        // ->form(function ($record) {
        //     $notification = new DefenseAuthorizedNotification($record);

        //     self::$emailBody = $notification->toMail(auth()->user()->email)->render();

        //     return [
        //         // \Filament\Forms\Components\MarkdownEditor::make('email_body')
        //         //     ->label('Email Body'),
        //         \Filament\Forms\Components\Placeholder::make('Preview')->hiddenLabel()
        //             ->content(self::$emailBody),
        //         // Forms\Components\RichEditor::make('description')
        //         //     ->columnSpanFull(),
        //         \Filament\Forms\Components\TagsInput::make('emails')
        //             ->label('Emails')
        //             ->splitKeys(['Tab', ',', ';'])
        //             ->nestedRecursiveRules([
        //                 'min:2',
        //                 'max:50',
        //             ])
        //             ->placeholder(__('Enter emails separated by commas'))
        //             ->default(function () use ($record) {
        //                 // Assuming getEmails is a method that takes the $record and returns an array of emails
        //                 return $record ? self::getEmails($record) : [];
        //             }),
        //         // Uncomment and adjust the rules as necessary
        //         // ->rules('required', 'email', 'max:255'),
        //     ];
        // })
        // ->color('success');

        return $static;
    }

    public static function getEmails($project): array
    {
        $administrators = \App\Models\User::administrators()->pluck('email');
        $AdministrativeSupervisor = \App\Models\User::where('assigned_program', $project->internship_agreement->student->program->value)
            ->where('role', \App\Enums\Role::AdministrativeSupervisor->value)
            ->pluck('email');
        $jury = $project->professors->pluck('email');
        $externalJury = $project->external_supervisor_email;
        $extraEmails = ['entreprises@inpt.ac.ma'];
        self::$emails = $jury->merge($externalJury)->merge($AdministrativeSupervisor)->merge($extraEmails)->toArray();

        return self::$emails;
    }
}
