<?php

namespace App\Models;


class ProfessorJury extends Core\BackendBaseModel 
{
    protected $table = 'professor_jury';

    public function professor() {
        return $this->belongsTo(Professor::class);
    }

    public function jury() {
        return $this->belongsTo(Jury::class);
    }
}