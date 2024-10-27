<?php

namespace App\Models;

use App\Enums;
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
        'central_organization',
        'status',
        'created_by_student_id',
    ];

    protected $casts = [
        'status' => Enums\OrganizationStatus::class,
    ];

    protected static function booted(): void
    {

        static::creating(function (Organization $organization) {
            $organization->status = Enums\OrganizationStatus::Published;
            $organization->created_by_student_id = auth()->id();

        });
    }

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

    public function scopeActive($query)
    {
        return $query->where('status', Enums\OrganizationStatus::Active)
            ->orWhere('status', Enums\OrganizationStatus::Published);
    }
}
