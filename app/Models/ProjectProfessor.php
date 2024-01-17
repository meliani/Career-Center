<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectProfessor extends Pivot
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
