<?php

namespace App\Filament\Actions\Action;

use App\Notifications\SendStudentsInfoNotification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class SendStudentsEmailAction extends BulkAction
{
    public static function getDefaultName(): string
    {
        return __('Share Students Info');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->slideOver()
            ->icon('heroicon-o-share')
            ->action(function (array $data): void {
                $students = $this->getRecords();

                // Generate URL with filter query parameter if needed
                $expiresAt = now()->addDays(7);
                $queryParams = [];
                
                if ($data['only_with_cv']) {
                    $queryParams['filter_cv'] = 'true';
                }
                
                $url = URL::temporarySignedRoute(
                    'students.info.preview',
                    $expiresAt,
                    array_merge(['studentIds' => $students->pluck('id')->toArray()], $queryParams)
                );

                // Send notification to each email
                foreach ($data['emails'] as $email) {
                    Notification::route('mail', $email)
                        ->notify(new SendStudentsInfoNotification(
                            $students,
                            $data['email_body'],
                            $data['subject'],
                            $url
                        ));
                }

                // Show success notification
                \Filament\Notifications\Notification::make()
                    ->title(__('Student information shared successfully'))
                    ->success()
                    ->duration(5000)
                    ->send();
            })
            ->steps([
                Step::make('Preview')
                    ->label(__('Preview'))
                    ->description(__('Preview email'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('subject')
                            ->label('Email Subject')
                            ->required()
                            ->default(__('Student Information')),
                        \Filament\Forms\Components\RichEditor::make('email_body')
                            ->label('Email Content')
                            ->default(function () {
                                return view('emails.students-share', [
                                    'count' => $this->getRecords()->count(),
                                ])->render();
                            })
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('only_with_cv')
                            ->label('Only show students with CV')
                            ->helperText('Filter the shared view to only show students who have a CV uploaded')
                            ->default(false),
                    ]),

                Step::make('Recipients')
                    ->label(__('Recipients'))
                    ->description(__('View and edit recipients'))
                    ->schema([
                        \Filament\Forms\Components\TagsInput::make('emails')
                            ->label('Emails')
                            ->splitKeys(['Tab', ',', ';', ' '])
                            ->nestedRecursiveRules(['email'])
                            ->placeholder(__('Enter emails separated by commas')),
                    ]),
            ])
            ->modalWidth('lg')
            ->color('success');
    }
}
