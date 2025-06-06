<?php

namespace App\Models;

use App\Enums;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use NumberFormatter;
use Parfaitementweb\FilamentCountryField\Traits\HasData;
use Spatie\Tags\HasTags;
use willvincent\Rateable\Rateable;

class InternshipOffer extends Model implements Viewable
{
    use HasData;
    use HasFactory;
    use HasTags;
    use InteractsWithViews;
    use Rateable;
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
        'recruiting_type',
        'application_email',
        'application_link',
        'number_of_students_requested',
        'status',
        'applyable',
        'expire_at',
        'organization_id',
        'expertise_field_id',
    ];

    protected $casts = [
        'recruiting_type' => Enums\RecruitingType::class,
        'status' => Enums\OfferStatus::class,
        'internship_level' => Enums\InternshipLevel::class,
        'applyable' => 'boolean',
        'expire_at' => 'date',
        'internship_type' => Enums\InternshipType::class,
        'currency' => Enums\Currency::class,
        'organization_type' => Enums\OrganizationType::class,
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

    public function expertiseField()
    {
        return $this->belongsTo(ExpertiseField::class);
    }

    // Cache year relationship
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function getYearAttribute()
    {
        return Cache::remember(
            'internship_offer_year_' . $this->year_id,
            Year::CACHE_DURATION,
            fn () => $this->year()->first()
        );
    }

    // Add scope for current year
    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('year_id', Year::current()->id);
    }

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

    public function getViewsCountAttribute()
    {
        $viewsCount = views($this)->count();

        if ($viewsCount === 0) {
            return null;
        }

        return $this->formatNumber($viewsCount) . ' ' . trans_choice('view|views', $viewsCount);
    }

    public function getUniqueViewsCountAttribute()
    {
        $uniqueViewsCount = views($this)->unique()->count();

        if ($uniqueViewsCount === 0) {
            return null;
        }

        return $this->formatNumber($uniqueViewsCount) . ' ' . trans_choice('visitor|visitors', $uniqueViewsCount);
    }

    public function getViewsSummaryAttribute()
    {
        $viewsCount = $this->getViewsCountAttribute();
        $uniqueViewsCount = $this->getUniqueViewsCountAttribute();

        if ($viewsCount === null && $uniqueViewsCount === null) {
            return null;
        }

        return ($viewsCount ?? __('No views yet')) . ' ' . __('by') . ' ' . ($uniqueViewsCount ?? __('No unique views yet'));
    }

    protected function formatNumber($number)
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::DECIMAL);

        return $formatter->format($number);
    }

    public function scopeActive($query)
    {
        return $query->withoutTrashed()
            ->where('year_id', Year::current()->id)
            ->where(function ($query) {
                $query->where('status', Enums\OfferStatus::Published)
                    ->orWhere('status', Enums\OfferStatus::Submitted);
            });
    }

    public function scopePublished($query)
    {
        return $query->where('status', Enums\OfferStatus::Published)
            ->where('year_id', Year::current()->id);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', Enums\OfferStatus::Submitted)
            ->where('year_id', Year::current()->id);
    }
}
