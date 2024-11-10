<?php

namespace App\Filament\Actions\Action;

use App\Notifications\SendApplicationsNotification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class SendApplicationsEmailAction extends Action
{
    public static function getDefaultName(): string
    {
        return __('Share Applications');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()
            ->slideOver()
            ->icon('heroicon-o-share')
            ->action(function (array $data, $livewire): void {
                $internship = $livewire->getOwnerRecord();

                // Send notification to each email
                foreach ($data['emails'] as $email) {
                    Notification::route('mail', $email)
                        ->notify(new SendApplicationsNotification(
                            $internship,
                            $data['email_body'],
                            $data['subject']
                        ));
                }

                // Show success notification
                \Filament\Notifications\Notification::make()
                    ->title(__('Emails sent successfully'))
                    ->success()
                    ->duration(5000)
                    ->send();
            })
            ->steps(function ($livewire) {
                $internship = $livewire->getOwnerRecord();

                if (! $internship) {
                    return [];
                }

                $expiresAt = $internship->expire_at ?? now()->addDays(30);
                $url = URL::temporarySignedRoute(
                    'internship.applications.preview',
                    $expiresAt,
                    ['internship' => $internship->id]
                );

                $emailBody = view('emails.applications-share', [
                    'url' => $url,
                    'internship' => $internship,
                ])->render();

                return [
                    Step::make('Preview')
                        ->label(__('Preview'))
                        ->description(__('Preview email'))
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('subject')
                                ->label('Email Subject')
                                ->required()
                                ->default(__('Applications for : :title', ['title' => $internship->project_title])),
                            \Filament\Forms\Components\RichEditor::make('email_body')
                                ->label('Email Content')
                                ->default($emailBody)
                                ->required(),
                        ]),

                    Step::make('Recipients')
                        ->label(__('Recipients'))
                        ->description(__('View and edit recipients'))
                        ->schema([
                            \Filament\Forms\Components\TagsInput::make('emails')
                                ->label('Emails')
                                ->splitKeys(['Tab', ',', ';', ' '])
                                ->nestedRecursiveRules(['email'])
                                ->placeholder(__('Enter emails separated by commas'))
                                ->default(function () use ($internship) {
                                    return $internship->responsible_email
                                        ? [$internship->responsible_email]
                                        : [];
                                }),
                        ]),
                ];
            })
            ->modalWidth('lg')
            ->color('success');

        return $static;
    }
}
