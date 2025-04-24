<?php

namespace App\Filament\Actions\BulkAction;

use App\Notifications\ShareStudentsInfoNotification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class SendStudentsBulkEmailAction extends BulkAction
{
    public static function getDefaultName(): string
    {
        return 'shareStudentInfo';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Share Students Info'))
            ->icon('heroicon-o-share')
            ->color('success')
            ->slideOver()
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
                            ->required()
                            ->default(function () {
                                $count = $this->getRecords()->count();
                                return view('emails.students-share', [
                                    'count' => $count,
                                ])->render();
                            }),
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
            ->action(function (Collection $records, array $data): void {
                $expiresAt = now()->addDays(7);
                $queryParams = [];
                
                if ($data['only_with_cv']) {
                    $queryParams['filter_cv'] = 'true';
                }
                
                $url = URL::temporarySignedRoute(
                    'students.info.preview',
                    $expiresAt,
                    array_merge(['studentIds' => $records->pluck('id')->toArray()], $queryParams)
                );

                foreach ($data['emails'] as $email) {
                    Notification::route('mail', $email)
                        ->notify(new ShareStudentsInfoNotification(
                            $records,
                            $data['email_body'],
                            $data['subject'],
                            $url
                        ));
                }

                \Filament\Notifications\Notification::make()
                    ->title(__('Student information shared successfully'))
                    ->success()
                    ->duration(5000)
                    ->send();
            });
    }
}
