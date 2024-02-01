<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    protected $table = 'jury_professor';
    protected $fillable = ['jury_id', 'professor_id', 'is_president', 'role'];
    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'jury_professor')
            ->withPivot('is_president', 'role')
            ->withTimestamps();
    }
    public function professor()
    {
        return $this->hasOne(Professor::class, 'id', 'professor_id');
    }
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
