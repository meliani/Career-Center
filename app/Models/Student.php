<?php

namespace App\Models;

use App\Models\School\Internship\Project;
use App\Models\School\Stream;
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

        if (app()->isProduction()) {
            static::addGlobalScope(function ($query) {
                $query
                // ->where('model_status_id', config('school.current.model_status.prod'))
                    ->where('year_id', config('school.current.year_id'));
            });
        }
    }

    public function setPin(Student $student, $currentPin, $streamOrder)
    {
        $student->pin = $streamOrder.str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    public function internship()
    {
        return $this->hasOne(Internship::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id', 'student_id');
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
