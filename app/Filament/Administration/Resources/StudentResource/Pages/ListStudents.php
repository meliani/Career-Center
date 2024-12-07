<?php

namespace App\Filament\Administration\Resources\StudentResource\Pages;

use App\Filament\Administration\Resources\StudentResource;
use App\Models\Student;
use App\Models\Year;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('PastEmailsToChange')
                ->label('Update Students by Email and ID')
                ->form([
                    \Filament\Forms\Components\Textarea::make('emails')
                        ->label('Emails')
                        ->placeholder('Paste emails here, separated by commas or new lines')
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('ids')
                        ->label('IDs')
                        ->placeholder('Paste IDs here, separated by commas or new lines')
                        ->required(),
                    \Filament\Forms\Components\Select::make('level')
                        ->label('New Level')
                        ->options([
                            'FirstYear' => 'First Year',
                            'SecondYear' => 'Second Year',
                            'ThirdYear' => 'Third Year',
                            'AlumniTransitional' => 'Alumni Transitional',
                            'Alumni' => 'Alumni',
                        ])
                        ->required(),
                    \Filament\Forms\Components\Select::make('year_id')
                        ->label('New Year')
                        ->options(Year::all()->pluck('title', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $emails = preg_split('/[\s,]+/', $data['emails']);
                    $ids = preg_split('/[\s,]+/', $data['ids']);

                    // Ensure the number of emails and IDs match
                    if (count($emails) !== count($ids)) {
                        Notification::make()
                            ->title('Error')
                            ->body('The number of emails and IDs must match.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Process emails and IDs together
                    $students = Student::whereIn('email', $emails)->get()->keyBy('email');

                    // Get the emails of the students found in the database
                    $foundEmails = $students->keys()->toArray();

                    // Find the emails that were not found in the database
                    $notFoundEmails = array_diff($emails, $foundEmails);

                    // Update the students
                    foreach ($emails as $index => $email) {
                        if (isset($students[$email])) {
                            $students[$email]->update([
                                'level' => $data['level'],
                                'year_id' => $data['year_id'],
                                'id_pfe' => $ids[$index], // Update the pin with the corresponding ID
                            ]);
                        }
                    }

                    // Notify the user about the result
                    $message = 'Students updated successfully.';
                    if (! empty($notFoundEmails)) {
                        $message .= ' Some emails were not found: ' . implode(', ', $notFoundEmails) . '.';
                    }

                    Notification::make()
                        ->title('Success')
                        ->body($message)
                        ->success()
                        ->sendToDatabase(Auth::user());
                })
                ->visible(fn () => auth()->user()->isAdministrator()),
        ];
    }
}
