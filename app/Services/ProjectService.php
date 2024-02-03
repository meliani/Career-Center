<?php

namespace App\Services;

use App\Models\DefenseSchedule;
use App\Models\Internship;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use App\Models\Professor;
use App\Models\Jury;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions\Action;

class ProjectService
{
    protected $forceOverwrite = true;

    public static function setForceOverwrite($value)
    {
        self::$forceOverwrite = $value;
    }
    public static function AssignInternshipsToProjects($record)
    {
        $assignedInternships = 0;
        $forceOverwrite = true;
        try {
            if (Gate::denies('batch-assign-internships-to-projects', )) {
                throw new AuthorizationException();
            }
            $signedInternships = Internship::where('status', '=', 'Signed')->get();

            foreach ($signedInternships as $signedInternship) {
                try {
                    $project = Project::create([
                        'id' => $signedInternship->id,
                        'id_pfe' => $signedInternship->id_pfe,
                        'title' => $signedInternship->title,
                        'description' => $signedInternship->description,
                        'organization' => $signedInternship->organization_name,
                        'start_date' => $signedInternship->starting_at,
                        'end_date' => $signedInternship->ending_at,
                    ]);
                    $signedInternship->project_id = $project->id;
                    $signedInternship->save();
                } catch (\Exception $e) {
                    if ($e->getCode() == 23000) {
                        // duplicate entry
                        if ($forceOverwrite) {
                            $project = Project::find($signedInternship->id);
                            $project->id_pfe = $signedInternship->id_pfe;
                            $project->title = $signedInternship->title;
                            $project->description = $signedInternship->description;
                            $project->organization = $signedInternship->organization;
                            $project->start_date = $signedInternship->starting_at;
                            $project->end_date = $signedInternship->ending_at;
                            $project->save();
                        } else {
                            continue;
                        }
                    } else {
                        Log::error($e->getMessage());
                        throw new \Exception('There was an error creating the project. Please try again.');
                    }
                }
                $signedInternship->project_id = $project->id;
                $signedInternship->save();
                $assignedInternships++;
                // if ($assignedInternships == 5) {
                //     break;
                // }
            }
        } catch (AuthorizationException $e) {
            Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();
            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
        Notification::make()
            ->title($assignedInternships . ' internships assigned to projects')
            ->success()
            ->send();
    }
    public static function GenerateProjectsJury($record)
    {
        $supervisors = collect();
        $projects = Project::all();
        $projects->each(function ($project) use ($record, $supervisors) {
            if ($project->internship->int_adviser_name != null && $project->internship->int_adviser_name != 'NA') {
                $supervisorName = $project->internship->int_adviser_name;
                $supervisors = $supervisors->push($supervisorName);
                $professor = Professor::where('name', 'like', '%'.$supervisorName.'%')->first();
                $professors[] = $professor;
                if ($professor != null) {
                    if ($project->id_pfe == null) {
                        $project->id_pfe = $project->id;
                    }
                    $jury = Jury::create([
                        'project_id' => $project->id,
                    ]);
                    $professor->juries()->attach($jury, ['role' => 'supervisor']);
                    
                }

            }
        });
        Notification::make()
        ->title($supervisors . ' internships assigned to projects')
        ->success()
        ->send();
    }
}

// public function internshipContracts(): BelongsToMany
    // {
    //     return $this->belongsToMany(InternshipContract::class, 'project_internship_contract')
    //         ->withTimestamps()
    //         ->limit(2);
    // }
    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->withPivot('role')
    //         ->withTimestamps();
    // }

    // public function worker(): HasMany
    // {
    //     return $this->hasMany(User::class)->where('role', 'worker');
    // }

    // public function supervisors(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->wherePivot('role', ProjectRoleEnum::supervisor())
    //         ->using(ProjectUser::class);
    // }

    // public function hasMaxSupervisorsReached(): bool
    // {
    //     return $this->supervisors()->count() >= 2; // Adjust the maximum number of supervisors as needed
    // }
    // public function hasMaxWorkersReached(): bool
    // {
    //     return $this->workers()->count() >= 2; // Adjust the maximum number of supervisors as needed
    // }

    // public function workers(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'project_user')
    //         ->wherePivot('role', ProjectRoleEnum::worker()->value);
    // }

    // public function assignStudent(Student $student, array $attributes = []): void
    // {
    //     if ($this->hasMaxWorkersReached()) {
    //         throw new \Exception('Maximum number of workers reached');
    //     }

    //     $this->users()->attach(
    //         $student, $attributes ??
    //         [
    //             'role' => ProjectRoleEnum::worker(),
    //         ]
    //     );
    // }
    // public function assignProfessor(Professor $professor, array $attributes = []): void
    // {
    //     if ($this->hasMaxSupervisorsReached()) {
    //         throw new \Exception('Maximum number of supervisors reached');
    //     }
    //     $this->users()->attach(
    //         $professor, $attributes ??
    //         [
    //             'role' => ProjectRoleEnum::supervisor(),
    //         ]
    //     );
    // }