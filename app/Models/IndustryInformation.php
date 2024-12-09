<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_fr',
        'name_ar',
        'slug',
        'description_en',
        'description_fr',
        'description_ar',
        'parent_id',
        'icon',
        'locale',
    ];

    public function parent()
    {
        return $this->belongsTo(IndustryInformation::class, 'parent_id');
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"name_$locale"} ?? $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"description_$locale"} ?? $this->description_en;
    }
}
