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
    protected $connection = 'backend_database';
    protected $fillable = [
        'id_pfe',
        'title',
        'organization',
        'description',
        'start_date',
        'end_date',
        'jury_id',
    ];
    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }
    public function internship(): HasOne
    {
        return $this->hasOne(Internship::class)->latestOfMany();
    }
    public function jury() {
        return $this->hasOne(Jury::class);
    }
}
