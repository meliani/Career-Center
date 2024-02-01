<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'jury_professor')
            ->withPivot('is_president', 'role')
            ->withTimestamps();
    }
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
