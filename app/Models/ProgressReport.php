<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressReport extends Model
{
    // ...existing code...
    protected $fillable = [
        'professor_project_id',
        'title',
        'content',
        'submitted_at',
    ];

    public function professorProject()
    {
        return $this->belongsTo(ProfessorProject::class);
    }
}
