<?php

namespace App\Models;

use App\Models\Core\baseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\School\Internship\Internship;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Profile\Professor;
use App\Models\Profile\Student;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class Person extends baseModel
{

    protected $table = 'people';

    protected $appends = [
        'full_name',
        'long_full_name'
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
        'program_id',
        'branche_id',
        'filiere_text',
        'is_mobility',
        'abroad_school',
        'year_id',
        'is_active',
        'model_status_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getFullNameAttribute()
    {
        // dd($this->attributes);
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
    public function getLongFullNameAttribute()
    {
        return $this->getTitleAttribute() . ' ' . $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
    public function getTitleAttribute()
    {
        if ($this->attributes['gender_id'] == 0)
            return "Mme";
        elseif ($this->attributes['gender_id'] == 1)
            return "M.";
        else
            return "Mme/M.";
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
