<?php

namespace App\Models;

use App\Enums;
use App\Models\Core\BackendBaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Student extends BackendBaseModel 
{
    protected $appends = [
        'full_name',
        'long_full_name',
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
        'graduated_at',
    ];

    protected $casts = [
        'title' => Enums\Title::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'program' => Enums\Program::class,
        'level' => Enums\StudentLevel::class,

    ];

    public static function boot(){
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query
                ->where('year_id', 7);
        });
    }

    public function setPin(Student $student, $currentPin, $streamOrder){
        $student->pin = $streamOrder.str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    public function internship(){
        return $this->belongsTo(InternshipAgreement::class);
    }

    public function projects(){
        return $this->belongsToMany(Project::class);
    }
    
    public function activeInternshipAgreement() {
        return $this->hasOne(InternshipAgreement::class)->where('active', true);
    }

    public function inactiveInternshipAgreements() {
        return $this->hasMany(InternshipAgreement::class)->where('active', false);
    }

    public function getFullNameAttribute(){
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function getLongFullNameAttribute(){
        return $this->title->getLabel().' '.$this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function scopeActive($query){
        return $query->where('is_active', true);
    }
}
