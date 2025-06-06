<?php

namespace App\Filament\Actions\Action;

use App\Models\Project;
use App\Models\ProjectAgreement;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SeparateBinomeAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'separate_binome';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Separate Binome'))
            ->icon('heroicon-o-scissors')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('Separate Binome'))
            ->modalDescription(__('This will separate the binome into individual projects. Each student will have their own project with the same details.'))
            ->modalSubmitActionLabel(__('Separate'))
            ->visible(fn (Model $record): bool => $this->canSeparateProject($record))
            ->action(fn (Model $record) => $this->separateProject($record));
    }

    protected function canSeparateProject(Project $project): bool
    {
        // Check if project has more than one agreement (binome)
        return $project->agreements()->count() > 1;
    }    protected function separateProject(Project $project): void
    {
        // Add logging for debugging
        \Log::info('SeparateBinomeAction: Starting separation for project ID: ' . $project->id);
        
        try {
            DB::transaction(function () use ($project) {
                $agreements = $project->agreements()->with('agreeable.student')->get();
                
                \Log::info('SeparateBinomeAction: Found ' . $agreements->count() . ' agreements');
                
                if ($agreements->count() <= 1) {
                    \Log::info('SeparateBinomeAction: Cannot separate - only ' . $agreements->count() . ' agreements');
                    Notification::make()
                        ->title(__('Cannot separate'))
                        ->body(__('This project does not have multiple students to separate.'))
                        ->warning()
                        ->send();
                    return;
                }

                // Keep the first agreement with the original project
                $firstAgreement = $agreements->first();
                $remainingAgreements = $agreements->skip(1);
                
                \Log::info('SeparateBinomeAction: Processing ' . $remainingAgreements->count() . ' remaining agreements');

                foreach ($remainingAgreements as $agreement) {
                    // Create a new project for each remaining agreement
                    $newProject = $project->replicate();
                    $newProject->save();
                    
                    \Log::info('SeparateBinomeAction: Created new project with ID: ' . $newProject->id);

                    // Copy the professors relationship
                    foreach ($project->professors as $professor) {
                        $newProject->professors()->attach($professor->id, [
                            'jury_role' => $professor->pivot->jury_role,
                            'created_by' => $professor->pivot->created_by,
                            'updated_by' => $professor->pivot->updated_by,
                            'approved_by' => $professor->pivot->approved_by,
                            'is_president' => $professor->pivot->is_president,
                            'votes' => $professor->pivot->votes,
                            'was_present' => $professor->pivot->was_present,
                        ]);
                    }

                    // Move the agreement to the new project
                    $agreement->update(['project_id' => $newProject->id]);

                    // Copy the timetable if it exists
                    if ($project->timetable) {
                        $newTimetable = $project->timetable->replicate();
                        $newTimetable->project_id = $newProject->id;
                        $newTimetable->save();
                    }

                    // Copy comments if they exist
                    foreach ($project->filamentComments as $comment) {
                        $newComment = $comment->replicate();
                        $newComment->commentable_id = $newProject->id;
                        $newComment->save();
                    }
                }

                // Update the original project title to reflect it's now individual
                $studentName = $firstAgreement->agreeable->student->name ?? 'Student';
                $originalTitle = $project->title;
                
                // Add student name to title if not already present
                if (!str_contains($originalTitle, $studentName)) {
                    $project->update([
                        'title' => $originalTitle . ' - ' . $studentName
                    ]);
                }

                // Update titles for new projects
                foreach ($remainingAgreements as $index => $agreement) {
                    $studentName = $agreement->agreeable->student->name ?? 'Student';
                    $newProject = Project::where('id', '>', $project->id)
                        ->orderBy('id')
                        ->skip($index)
                        ->first();
                    
                    if ($newProject && !str_contains($newProject->title, $studentName)) {
                        $newProject->update([
                            'title' => $originalTitle . ' - ' . $studentName
                        ]);
                    }
                }
                
                \Log::info('SeparateBinomeAction: Separation completed successfully');
            });

            Notification::make()
                ->title(__('Binome separated successfully'))
                ->body(__('The binome has been separated into individual projects.'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Log::error('SeparateBinomeAction: Error - ' . $e->getMessage());
            \Log::error('SeparateBinomeAction: Stack trace - ' . $e->getTraceAsString());
            
            Notification::make()
                ->title(__('Error separating binome'))
                ->body(__('An error occurred while separating the binome: ') . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
