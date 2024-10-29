<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertiseField extends Model
{
    use HasFactory;

    protected $casts = [
        'programs' => 'array',
    ];
}
