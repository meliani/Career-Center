<?php

namespace App\Models;

use App\Enum\ProjectRoleEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    // use HasFactory;
    // use HasUuids;
    protected $connection = 'mysql';
    
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'project_professor')
        ->withPivot('role');
        // ->withTimestamps();
    }
    public function jury()
    {
        return $this->belongsTo(Jury::class);
    }

}
