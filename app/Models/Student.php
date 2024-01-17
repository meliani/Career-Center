<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Student extends Person implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'people';
    // protected $primaryKey = "id";

    protected $appends = [
        'full_name',
    ];

    public static function boot()
    {
        parent::boot();

        // if (app()->isProduction()) {
        static::addGlobalScope(function ($query) {
            $query
            // ->where('model_status_id', config('school.current.model_status.prod'))
                ->where('year_id', 7);
        });
        // }
    }

    public function setPin(Student $student, $currentPin, $streamOrder)
    {
        $student->pin = $streamOrder.str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    /* New edits for a new logic by mel */
    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
    /* End edits for a new logic by mel */

    /* commented by mel by implementing the other field */

    // public function internship()
    // {
    //     return $this->hasOne(Internship::class);
    // }

    public function program()
    {
        return $this->belongsTo(Program::class);
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
