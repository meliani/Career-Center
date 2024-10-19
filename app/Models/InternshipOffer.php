<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Parfaitementweb\FilamentCountryField\Traits\HasData;
use Spatie\Tags\HasTags;

class InternshipOffer extends Model
{
    use HasData;
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected $table = 'internship_offers';

    protected $fillable = [
        'year_id',
        'internship_level',
        'organization_name',
        'organization_type',
        'country',
        'internship_type',
        'responsible_fullname',
        'responsible_occupation',
        'responsible_phone',
        'responsible_email',
        'project_title',
        'project_details',
        'internship_location',
        'keywords',
        'attached_file',
        'link',
        'internship_duration',
        'remuneration',
        'currency',
        'recruting_type',
        'application_email',
        'application_link',
        'number_of_students_requested',
        'status',
        'applyable',
        'expire_at',
        'organization_id',
    ];

    protected $casts = [
        'recruting_type' => Enums\RecrutingType::class,
        'status' => Enums\OfferStatus::class,
        'internship_level' => Enums\InternshipLevel::class,
        'applyable' => 'boolean',
        'expire_at' => 'date',
        'internship_type' => Enums\InternshipType::class,
        'currency' => Enums\Currency::class,
        'organization_type' => Enums\OrganizationType::class,
        'countr',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // protected $appends = [
    //     'internship_duration',
    // ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new Scopes\InternshipOfferScope);

    }

    protected static function booted(): void
    {
        static::deleting(function (InternshipOffer $internshipOffer) {
            $internshipOffer->tags()->detach();
        });

    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // public function getInternshipDurationAttribute(): string
    // {
    //     return $this->internship_duration . ' ' . __('months');
    // }
    public function getCountry()
    {
        return $this->attributes['country'] ?? null; // Example logic
    }

    public function getCountryAttribute()
    {
        return $this->getCountriesList()[$this->getCountry()] ?? null;
    }

    public function publish()
    {
        $this->status = Enums\OfferStatus::Published;
        $this->save();

    }

    public function applications()
    {
        return $this->hasMany(InternshipApplication::class);
    }
}
