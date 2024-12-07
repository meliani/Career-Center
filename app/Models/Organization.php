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
        'slug',
        'website',
        'address',
        'city',
        'country',
        'parent_organization',
        'status',
        'created_by_student_id',
        'industry_information_id',
    ];

    protected $casts = [
        'status' => Enums\OrganizationStatus::class,
    ];

    protected static function booted(): void
    {

        static::creating(function (Organization $organization) {
            $organization->status = Enums\OrganizationStatus::Published;
            $organization->created_by_student = auth()->id();

        });
    }

    public function parentOrganization()
    {
        return $this->belongsTo(Organization::class, 'parent_organization');
    }

    public function childOrganizations()
    {
        return $this->hasMany(Organization::class, 'parent_organization');
    }

    public function createdByStudent()
    {
        return $this->belongsTo(Student::class, 'created_by_student_id');
    }

    public function industryInformation()
    {
        return $this->belongsTo(IndustryInformation::class);
    }

    public function internshipOffers()
    {
        return $this->hasMany(InternshipOffer::class);
    }

    public function internshipAgreementContacts()
    {
        return $this->hasMany(InternshipAgreementContact::class);
    }

    public function finalYearInternshipAgreements()
    {
        return $this->hasMany(FinalYearInternshipAgreement::class);
    }

    public function apprenticeshipAgreements()
    {
        return $this->hasMany(Apprenticeship::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
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

    public function getWebsiteUrlAttribute()
    {
        if (! $this->website) {
            return null;
        }

        return str_starts_with($this->website, 'http') ? $this->website : "https://{$this->website}";
    }
}
