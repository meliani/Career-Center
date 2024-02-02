<?php

namespace App\Models;

use App\Enum\ProjectRoleEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    // use HasFactory;
    // use HasUuids;
    protected $connection = 'mysql';
    protected $fillable = [
        'title',
        'description',
        'role',
        'jury_id',
        'internship_id',
    ];
    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }
    public function internship(): HasOne
    {
        return $this->hasOne(Internship::class)->latestOfMany();
    }
    // public function jury(): BelongsTo
    // {
    //     return $this->belongsTo(Jury::class);
    // }

    // public function juries(): HasMany
    // {
    //     return $this->hasMany(Jury::class, 'jury_id', 'jury_id');
    // }
    public function jury() {
        return $this->hasOne(Jury::class);
    }

    public function professors(): HasManyThrough
    {
        return $this->hasManyThrough(Professor::class, Jury::class, 'project_id', 'jury_id', 'id', 'professor_id');
    }


}
