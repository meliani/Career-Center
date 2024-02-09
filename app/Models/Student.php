<?php

namespace App\Models;

use App\Enums;
use App\Models\Core\BackendBaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Student extends BackendBaseModel implements HasMedia
{
    use InteractsWithMedia;

    // protected $table = 'people';
    // protected $primaryKey = "id";

    protected $appends = [
        'full_name',
    ];

    protected $fillable = [
        'title',
        'pin',
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
        'parrain_titre',
        'encadrant_ext_titre',
        'encadrant_int_titre',
    ];

    protected $casts = [
        'title' => Enums\Title::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'program' => Enums\Program::class,
        'parrain_titre' => Enums\Title::class,
        'encadrant_ext_titre' => Enums\Title::class,
        'encadrant_int_titre' => Enums\Title::class,
        'level' => Enums\StudentLevel::class,

    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query
                ->where('year_id', 7);
        });
    }

    public function setPin(Student $student, $currentPin, $streamOrder)
    {
        $student->pin = $streamOrder.str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    public function internship()
    {
        return $this->belongsTo(InternshipAgreement::class);
    }

    // public function teammate()
    // {
    //     return $this->hasOne(Student::class, 'teammate_id');
    // }
    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('internship')
            ->useDisk('userfiles');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function getLongFullNameAttribute()
    {
        return $this->title->getLabel().' '.$this->attributes['first_name'].' '.$this->attributes['last_name'];
    }
}
