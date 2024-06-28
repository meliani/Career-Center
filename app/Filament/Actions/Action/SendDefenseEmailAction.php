<?php

namespace App\Filament\Actions\Action;

use App\Models\Project;
use Filament\Tables\Actions\Action;

class SendDefenseEmailAction extends Action
{
    public static array $emails = [];

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

        })->form(function ($record) {
            return [
                \Filament\Forms\Components\TagsInput::make('emails')
                    ->label('Emails')
                    ->placeholder(__('Enter emails separated by commas'))
                    ->default(function () use ($record) {
                        // Assuming getEmails is a method that takes the $record and returns an array of emails
                        return $record ? self::getEmails($record) : [];
                    }),
                // Uncomment and adjust the rules as necessary
                // ->rules('required', 'email', 'max:255'),
            ];
        })
            ->requiresConfirmation();

        return $static;
    }

    public static function getEmails($project): array
    {
        $administrators = \App\Models\User::administrators()->pluck('email');
        $programUsers = \App\Models\User::where('assigned_program', $project->internship_agreement->student->program->value)->pluck('email');
        $jury = $project->professors->pluck('email');
        $externalJury = $project->external_supervisor_email;
        $extraEmails = ['entreprises@inpt.ac.ma'];
        self::$emails = $jury->merge($externalJury)->merge($programUsers)->merge($extraEmails)->toArray();

        return self::$emails;
    }
}
