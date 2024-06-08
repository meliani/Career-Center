<?php

namespace App\Models;

use App\Enums;
use App\Notifications;
use Illuminate\Support\Facades\Notification;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Core\BackendBaseModel
{
    use HasFilamentComments;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Scopes\ProjectScope());
    }

    protected static function booted(): void
    {

        static::updated(function ($project) {
            $admins = \App\Models\User::administrators();
            Notification::send($admins, new Notifications\ProjectUpdated($project));
        });

    }

    // rules
    public static function rules($id = null)
    {
        return [
            'title' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    }

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
    ];

    protected $appends = [
        'id_pfe',
        'organization',
        'description',
        'assigned_department',
    ];

    protected $casts = [
        'start_date' => 'date:Y/m/d',
        'end_date' => 'date:Y/m/d',
    ];

    public function getIdPfeAttribute()
    {
        if ($this->hasTeammate()) {
            // dd($this->internship_agreements()->first()->id_pfe, $this->internship_agreements()->latest()->first()->id_pfe);

            return ($this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID') . ' ' . __('&') . ' ' . ($this->internship_agreements()->latest()->first() ? $this->internship_agreements()->latest()->first()->id_pfe : 'Undefined ID');
        } else {
            return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->id_pfe : 'Undefined ID';
        }
    }

    public function getOrganizationAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->organization_name : 'Undefined Organization';

    }

    public function getDescriptionAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->description : 'Undefined Internship';
    }

    public function getAssignedDepartmentAttribute()
    {

        if ($this->hasTeammate()) {
            return $this->internship_agreements()->first()?->assigned_department;

            return $this->internship_agreements()->first()?->assigned_department . ' ' . __('&') . ' ' . $this->internship_agreements()->latest()->first()?->assigned_department;
        } else {
            return $this->internship_agreements()->first()?->assigned_department;
        }
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function internship_agreements()
    {
        return $this->hasMany(InternshipAgreement::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_project')
            ->withPivot('jury_role', 'created_by', 'updated_by', 'approved_by', 'is_president', 'votes')->withTimestamps()
            ->using(ProfessorProject::class);

    }

    public function hasTeammate()
    {
        return $this->students()->count() > 1;
    }

    public function supervisor()
    {
        // dd($this->professors()
        //     ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value));

        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Supervisor->value);
    }

    public function reviewers()
    {

        return $this->professors()
            ->wherePivot('jury_role', Enums\JuryRole::Reviewer->value);
    }

    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }

    public function unplanned()
    {
        return $this->whereDoesntHave('timetable');
    }

    public function getDepartmentAttribute()
    {
        return $this->internship_agreements()->first() ? $this->internship_agreements()->first()->assigned_department : 'Undefined Department';
    }
}
