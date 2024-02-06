<?php

namespace App\Models;

use App\Enums;

class Person extends Core\FrontendBaseModel
{
    protected $table = 'people';

    protected $appends = [
        'full_name',
        'long_full_name',
    ];

    protected $guarded = [];

    protected $fillable = [
        'title',
        'pin',
        'full_name',
        'first_name',
        'last_name',
        'email_perso',
        'phone',
        'cv',
        'lm',
        'photo',
        'birth_date',
        'level',
        'program',
        'is_mobility',
        'abroad_school',
        'year_id',
        'is_active',
        'model_status_id',
        'graduated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',

        'program' => Enums\Program::class,
        'title' => Enums\Title::class,
        'level' => Enums\StudentLevel::class,
    ];

    public function getFullNameAttribute()
    {
        // dd($this->attributes);

        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function getLongFullNameAttribute()
    {
        return $this->getTitleAttribute().' '.$this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    // public function getTitleAttribute()
    // {
    //     if ($this->attributes['gender_id'] == 0) {
    //         return 'Mme';
    //     } elseif ($this->attributes['gender_id'] == 1) {
    //         return 'M.';
    //     } else {
    //         return 'Mme/M.';
    //     }
    // }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
