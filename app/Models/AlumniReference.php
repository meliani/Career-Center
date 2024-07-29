<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniReference extends Model
{
    use HasFactory;

    protected $table = 'alumni_references';

    protected $fillable = [
        'title',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'graduation_year_id',
        'degree',
        'program',
        'is_enabled',
        'is_mobility',
        'abroad_school',
        'work_status',
        'resume_url',
        'avatar_url',
        'number_of_bounces',
        'bounce_reason',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_mobility' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // protected $dates = [
    //     'created_at',
    //     'updated_at',
    // ];
}
