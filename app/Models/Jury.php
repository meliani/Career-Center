<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{

    protected $fillable = [
        'project_id',
        'timeslot_id',
    ];
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function professors() {
        return $this->belongsToMany(Professor::class, 'professor_jury')->withPivot('role');
    }
}
