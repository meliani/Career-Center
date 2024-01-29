<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class DefenseSchedule extends Model
{
    protected $fillable = [
        'starting_from',
        'ending_at',
        'score',
        'minutes_spent',
        'project_id',
    ];
    protected $casts = [
        'starting_from' => 'date',
        'ending_at' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}