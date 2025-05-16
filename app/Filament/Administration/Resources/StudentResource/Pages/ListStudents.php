<?php

namespace App\Filament\Administration\Resources\StudentResource\Pages;

use App\Filament\Administration\Resources\StudentResource;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\Year;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importStudents')
                ->label('Import Students')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('csv_file')
                        ->label('CSV File')
                        ->disk('local')
                        ->directory('temp')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel'])
                        ->required(),
                    \Filament\Forms\Components\Select::make('merge_mode')
                        ->label('Merge Mode')
                        ->options([
                            'update' => 'Update existing students',
                            'skip' => 'Skip existing students',
                            'replace' => 'Replace existing students',
                        ])
                        ->default('update')
                        ->required()
                        ->helperText('How to handle existing students found in the import file'),
                    \Filament\Forms\Components\Select::make('academic_year')
                        ->label('Academic Year')
                        ->options(\App\Models\Year::pluck('title', 'title'))
                        ->default(\App\Models\Year::current()->title)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/' . $data['csv_file']);
                    
                    try {
                        $import = new \App\Imports\StudentsImport(
                            $data['merge_mode'],
                            $data['academic_year']
                        );
                        
                        $import->import($filePath);
                        
                        // Get the results
                        $results = $import->getImportResults();
                        
                        // Show a notification with the results
                        \Filament\Notifications\Notification::make()
                            ->title('Import Completed')
                            ->body("Created: {$results['created']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}, Failed: {$results['failed']}")
                            ->success()
                            ->send();
                        
                        // If there were any errors, log them and inform the user
                        if (!empty($results['errors'])) {
                            // Log the detailed errors
                            \Illuminate\Support\Facades\Log::error('Student import errors', [
                                'errors' => $results['errors'],
                            ]);
                            
                            // Notify the user that there were errors
                            \Filament\Notifications\Notification::make()
                                ->title('Import Warnings')
                                ->body("There were {$results['failed']} errors during import. See application logs for details.")
                                ->warning()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Import Failed')
                            ->body("Error: {$e->getMessage()}")
                            ->danger()
                            ->send();
                        
                        \Illuminate\Support\Facades\Log::error('Student import failed', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                })
                ->visible(fn () => auth()->user()->isAdministrator()),
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
