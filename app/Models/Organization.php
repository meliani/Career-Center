<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Parfaitementweb\FilamentCountryField\Traits\HasData;

class Organization extends Model
{
    use HasData;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'office_location',
        'central_organization',
    ];

    public function centralOrganization()
    {
        return $this->belongsTo(Organization::class, 'central_organization');
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'central_organization');
    }

    public function getCountry()
    {
        return $this->attributes['country'] ?? null; // Example logic
    }

    public function getCountryAttribute()
    {
        return $this->getCountriesList()[$this->getCountry()];
    }
}
