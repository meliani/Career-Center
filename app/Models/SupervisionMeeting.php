<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisionMeeting extends Model
{
    // ...existing code...
    protected $fillable = [
        'professor_project_id',
        'scheduled_at',
        'location',
        'notes',
    ];

    public function professorProject()
    {
        return $this->belongsTo(ProfessorProject::class);
    }
}
