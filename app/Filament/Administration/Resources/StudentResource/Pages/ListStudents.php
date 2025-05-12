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
                ->label('Update Students by Email and IDs')
                ->form([
                    \Filament\Forms\Components\Textarea::make('emails')
                        ->label('Emails')
                        ->placeholder('Paste emails here, separated by commas or new lines')
                        ->required(),
                    \Filament\Forms\Components\Select::make('id_type')
                        ->label('ID Type to Import')
                        ->options([
                            'id_pfe' => 'ID PFE',
                            'konosys_id' => 'Konosys ID',
                            'both' => 'Both ID PFE and Konosys ID'
                        ])
                        ->helperText('For "Both" option, use format "id_pfe:konosys_id" or the same value will be used for both fields')
                        ->default('id_pfe'),
                    \Filament\Forms\Components\Textarea::make('ids')
                        ->label('IDs')
                        ->placeholder('Paste IDs here, separated by commas or new lines')
                        ->helperText('Optional if only updating level and year. Required if updating IDs.')
                        ->required(false),
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
                    
                    // If IDs are provided, validate them
                    $ids = isset($data['ids']) && trim($data['ids']) !== '' 
                        ? preg_split('/[\s,]+/', $data['ids']) 
                        : [];
                    
                    // Only validate if IDs are provided
                    if (!empty($ids) && count($emails) !== count($ids)) {
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
                            $updateData = [
                                'level' => $data['level'],
                                'year_id' => $data['year_id'],
                            ];
                            
                            // Add the appropriate ID field based on the selected type if IDs are provided
                            if (!empty($ids)) {
                                if ($data['id_type'] === 'id_pfe') {
                                    $updateData['id_pfe'] = $ids[$index];
                                } elseif ($data['id_type'] === 'konosys_id') {
                                    $updateData['konosys_id'] = $ids[$index];
                                } elseif ($data['id_type'] === 'both') {
                                    // For "both" option, we'll expect IDs to be in format "id_pfe:konosys_id"
                                    $idParts = explode(':', $ids[$index], 2);
                                    if (count($idParts) === 2) {
                                        $updateData['id_pfe'] = trim($idParts[0]);
                                        $updateData['konosys_id'] = trim($idParts[1]);
                                    } else {
                                        // If colon separator not found, use the same ID for both fields
                                        $updateData['id_pfe'] = $ids[$index];
                                        $updateData['konosys_id'] = $ids[$index];
                                    }
                                }
                            }
                            
                            $students[$email]->update($updateData);
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
