<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProfessorJury extends Model {
    // Assuming you have a table named 'professor_jury'
    protected $table = 'professor_jury';

    public function professor() {
        return $this->belongsTo(Professor::class);
    }

    public function jury() {
        return $this->belongsTo(Jury::class);
    }
}