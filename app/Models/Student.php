<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Enums;

class Student extends Person implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'people';
    // protected $primaryKey = "id";

    protected $appends = [
        'full_name',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'program' => Enums\Program::class,
        'parrain_titre' => Enums\Title::class,
        'encadrant_ext_titre' => Enums\Title::class,

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
        return $this->belongsTo(Internship::class);
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
}
