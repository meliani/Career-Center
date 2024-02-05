<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{

    protected $fillable = [
        'project_id',
        'timeslot_id',
        'room_id',
    ];
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function professors() {
        return $this->belongsToMany(Professor::class, 'professor_jury')->withPivot('role');
    }
    public function timeslot() {
        return $this->belongsTo(Timeslot::class);
    }
    public function room() {
        return $this->belongsTo(Room::class);
    }
}
