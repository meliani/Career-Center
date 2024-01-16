<?php

namespace App\Models;

use App\Models\Core\baseModel;

class Person extends baseModel
{
    protected $table = 'people';

    protected $appends = [
        'full_name',
        'long_full_name',
    ];

    protected $guarded = [];

    public $fillable = [
        'id',
        'gender_id',
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
        'current_year',
        'branche_id',
        'program',
        'is_mobility',
        'abroad_school',
        'year_id',
        'is_active',
        'model_status_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // public function getFullNameAttribute()
    // {
    //     // dd($this->attributes);

    //     return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    // }

    public function getLongFullNameAttribute()
    {
        return $this->getTitleAttribute().' '.$this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function getTitleAttribute()
    {
        if ($this->attributes['gender_id'] == 0) {
            return 'Mme';
        } elseif ($this->attributes['gender_id'] == 1) {
            return 'M.';
        } else {
            return 'Mme/M.';
        }
    }

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
