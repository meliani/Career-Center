<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProfessorProject extends Pivot
{
    protected $casts = [
        'jury_role' => Enums\JuryRole::class,
    ];
}
