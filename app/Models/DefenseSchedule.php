<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefenseTimeAutoSchedule extends Model
{
    protected $fillable = [
        'starts_at',
        'ends_at',
        'score',
        'project_id',

    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class);
    }
}
