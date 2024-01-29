<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use \App\Enums\Title;
use \App\Enums\Status;
use App\Casts\TitleCast;
use App\Casts\StatusCast;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget\Stat;
// use Illuminate\Database\Eloquent\Model;
use App\Models\Core\baseModel as Model;

class Internship extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        // dd(Title::Mr);

        // dd(Status::Draft);

    }
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\DepartmentCoordinator());
    }
    public function scopeFilterByProgramHead($query)
    {
        return $query->whereHas('student', function ($q) {
            $q->where('program', auth()->user()->program_coordinator);
        });
    }

    protected $guarded = [];
    protected $casts = [
        // 'title' => Title::class,
        'status' => Status::class,

        'starting_at' => 'date',
        'ending_at' => 'date',
        'validated_at' => 'datetime',
        'signed_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    /* Validate function to be exexuted only by SuperAdministrator Administrator ProgramCoordinator */

    public function validate(?String $department=null)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }

            $this->validated_at = now();
            // $this->department =$department;
            $this->save();
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function sign()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->signed_at = now();
            $this->save();
            Notification::make()
                ->title('Signed successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }
    public function receive()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->received_at = now();
            $this->save();
            Notification::make()
                ->title('Achieved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }
    public function assignDepartment($department)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->assigned_department = $department;
            $this->save();
            Notification::make()
                ->title('Assigned successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }
    public function changeStatus($status)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->status = $status;
            $this->save();
            Notification::make()
                ->title('Status changed successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }
    /* New edits for a new logic by mel from bottom to top */
    public function defenses()
    {
        return $this->belongsToMany(Defense::class, 'defense_internship');
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function project()
    {
        return $this->belongsToMany(Project::class);
    }
    /* End edits for a new logic by mel */

    public function binome()
    {
        return $this->belongsTo(Student::class, 'binome_user_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getParrainNameAttribute()
    {
        return $this->getTitle($this->parrain_titre).' '.$this->parrain_nom.' '.$this->parrain_prenom;
    }

    public function getEncadrantExtNameAttribute()
    {
        return $this->getTitle($this->encadrant_ext_titre).' '.$this->encadrant_ext_nom.' '.$this->encadrant_ext_prenom;
    }

    public function getDureeAttribute()
    {
        return $this->ending_at->diffInWeeks($this->starting_at).' semaines';
    }

    public function getDurationInMonthsAttribute()
    {
        return $this->ending_at->diffInMonths($this->starting_at).' mois';
    }
}
