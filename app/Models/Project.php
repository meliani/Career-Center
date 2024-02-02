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
    public function jury() {
        return $this->hasOne(Jury::class);
    }
    public function professors_jury() {
        return $this->hasManyThrough(
            Professor::class,
            Jury::class,
            'project_id', // Foreign key on the professor_jury table
            'id', // Local key on the juries table
            'id', // Local key on the professors table
            'professor_id' // Foreign key on the professor_jury table
        );
        }
    
    //     // Additional method to get professors with roles
    //     public function professorsWithRoles() {
    //         return $this->jury->professors()->withPivot('role');
    //     }
}
