<?php

namespace App\Models;

use App\Enums;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Internship extends Core\FrontendBaseModel
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
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

    public $fillable = [
        'id_pfe',
        'organization_name',
        'adresse',
        'city',
        'country',
        'office_location',
        'parrain_titre',
        'parrain_nom',
        'parrain_prenom',
        'parrain_fonction',
        'parrain_tel',
        'parrain_mail',
        'encadrant_ext_titre',
        'encadrant_ext_nom',
        'encadrant_ext_prenom',
        'encadrant_ext_fonction',
        'encadrant_ext_tel',
        'encadrant_ext_mail',
        'title',
        'description',
        'keywords',
        'starting_at',
        'ending_at',
        'remuneration',
        'currency',
        'load',
        'int_adviser_name',
        'student_id',
        'year_id',
        'is_valid',
        'status',
        'announced_at',
        'validated_at',
        'assigned_department',
        'received_at',
        'signed_at',
        'project_id',
        'observations',
    ];

    protected $casts = [
        'starting_at' => 'date',
        'ending_at' => 'date',
        'validated_at' => 'datetime',
        'signed_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'status' => Enums\Status::class,
        'parrain_titre' => Enums\Title::class,
        'encadrant_ext_titre' => Enums\Title::class,
        'assigned_department' => Enums\Department::class,
        'teammate_status' => Enums\TeammateStatus::class,

        // 'status' => 'string',
        // 'parrain_titre' => 'string',
        // 'encadrant_ext_titre' => 'string',
        // 'assigned_department' => 'string',
    ];
    /* Validate function to be exexuted only by SuperAdministrator Administrator ProgramCoordinator */

    public function validate(?string $department = null)
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
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
    public function teammate()
    {
        return $this->belongsTo(Student::class, 'teammate_id');
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
