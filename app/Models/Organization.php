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
            $organization->created_by_student_id = auth()->id();

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

    public function internshipAgreements()
    {
        return $this->hasMany(InternshipAgreement::class);
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
        return $this->getCountriesList()[$this->getCountry()] ?? null;
    }

    public function setCountryAttribute($value)
    {
        // If already a country code, store it directly
        if (array_key_exists($value, $this->getCountriesList())) {
            $this->attributes['country'] = $value;

            return;
        }

        // Find the country code by name
        $countryCode = array_search($value, $this->getCountriesList());
        $this->attributes['country'] = $countryCode ?: $value;
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

    public function getTotalAgreementsCountAttribute()
    {
        return $this->internshipAgreements()->count()
            + $this->finalYearInternshipAgreements()->count()
            + $this->apprenticeshipAgreements()->count();
    }

    public function getTotalContactsCountAttribute()
    {
        return $this->internshipAgreementContacts()->count();
    }

    public function getTotalRelatedCountAttribute()
    {
        return $this->total_agreements_count + $this->total_contacts_count;
    }
}
