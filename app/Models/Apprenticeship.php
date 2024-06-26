<?php

namespace App\Models;

use App\Enums;
use App\Services\UrlService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Period\Period;
use Spatie\Tags\HasTags;

class Apprenticeship extends Model
{
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected $table = 'apprenticeships';

    protected $appends = [
        'duration_in_weeks',
        'verification_string',
        'encoded_url',
        'decoded_url',
        'internship_period',
    ];

    protected $fillable = [
        'student_id',
        'year_id',
        'project_id',
        'status',
        'announced_at',
        'validated_at',
        'assigned_department',
        'received_at',
        'signed_at',
        'observations',
        'organization_id',
        'title',
        'description',
        'keywords',
        'starting_at',
        'ending_at',
        'remuneration',
        'currency',
        'workload',
        'parrain_id',
        'supervisor_id',
        'tutor_id',
        'pdf_path',

    ];

    protected $casts = [
        'assigned_department' => Enums\Department::class,
        'starting_at' => 'date',
        'ending_at' => 'date',
        'remuneration' => 'decimal:2',

    ];

    protected $dates = [
        'announced_at',
        'validated_at',
        'received_at',
        'signed_at',
        'starting_at',
        'ending_at',
    ];

    protected static function booted(): void
    {

        static::addGlobalScope(new Scopes\ApprenticeshipScope());
        static::creating(function (Apprenticeship $apprenticeship) {
            $apprenticeship->student_id = auth()->id();
            $apprenticeship->year_id = Year::current()->id;
            $apprenticeship->status = Enums\Status::Announced;

        });

    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function parrain()
    {
        return $this->belongsTo(ApprenticeshipAgreementContact::class, 'parrain_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(ApprenticeshipAgreementContact::class, 'supervisor_id', 'id');
    }

    public function getDurationInWeeksAttribute()
    {
        // if starting at is carbon instance

        if ($this->starting_at instanceof Carbon && $this->ending_at instanceof Carbon) {
            // return $this->starting_at->diffInWeeks($this->ending_at);
            // return $this->starting_at->floatDiffInRealWeeks($this->ending_at);
            return $this->starting_at->longAbsoluteDiffForHumans($this->ending_at);

        } else {
            return 0;
        }
    }

    public function getVerificationStringAttribute()
    {
        return $this->id . '-' . $this->student_id;
    }

    public function getEncodedUrlAttribute()
    {
        return UrlService::encodeUrl($this->verification_string);
    }

    public function getDecodedUrlAttribute()
    {
        return UrlService::decodeUrl($this->encoded_url);
    }

    public function setInternshipPeriodAttribute($value)
    {
        if (strpos($value, ' - ') !== false) {
            [$start, $end] = explode(' - ', $value);
            $this->attributes['starting_at'] = Carbon::createFromFormat('d/m/Y', $start);
            $this->attributes['ending_at'] = Carbon::createFromFormat('d/m/Y', $end);
        }
    }

    public function getPeriodAttribute()
    {
        return Period::make($this->starting_at, $this->ending_at);
    }

    public function getInternshipPeriodAttribute()
    {
        return $this->starting_at->format('d/m/Y') . ' - ' . $this->ending_at->format('d/m/Y');
    }
}
