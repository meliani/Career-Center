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

class Project extends Core\BackendBaseModel
{
    // use HasFactory;
    // use HasUuids;
    protected $fillable = [
        'id_pfe',
        'title',
        'organization',
        'description',
        'start_date',
        'end_date',
        'jury_id',
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
    public function internships(): HasMany
    {
        return $this->hasMany(InternshipAgreement::class);
    }
    public function LastInternship(): HasOne
    {
        return $this->hasOne(InternshipAgreement::class)->latestOfMany();
    }
    public function jury() {
        return $this->hasOne(Jury::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class)->withTimestamps();
    }
    // public function teammate(): BelongsTo
    // {
    //     return $this->belongsTo(Student::class, 'teammate_id');
    // }
}
