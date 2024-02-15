<?php

namespace App\Models;

use App\Enum\ProjectRoleEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Enums;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Project extends Core\BackendBaseModel
{
    use HasFilamentComments;
    protected static function boot()
    {
        parent::boot();
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\ProjectScope());
    }
    // rules 
    public static function rules($id = null)
    {
        return [
            'id_pfe' => 'required|max:10',
            'title' => 'required|max:255',
            'organization' => 'required|max:255',
            'description' => 'required|max:65535',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:'.implode(',', Enums\Status::getArray()),
            'has_teammate' => 'required|boolean',
            'teammate_status' => 'required|in:'.implode(',', Enums\TeammateStatus::getArray()),
            'teammate_id' => 'required|exists:students,id',
        ];
    }
    protected $fillable = [
        'id_pfe',
        'title',
        'organization',
        'description',
        'start_date',
        'end_date',
        'status',
        'has_teammate',
        'teammate_status',
        'teammate_id',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'teammate_status' => Enums\TeammateStatus::class,
        'status' => Enums\Status::class,
    ];
    public function students() {
        return $this->belongsToMany(Student::class,
        'project_student'
        );
    }
    public function internshipAgreements() {
        return $this->hasMany(InternshipAgreement::class);
    }
    public function professors() {
        return $this->belongsToMany(Professor::class, 'professor_project')->withPivot('jury_role');
    }
    public function timeslots() {
        return $this->belongsTo(Timeslot::class);
    }
}
