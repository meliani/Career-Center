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
        'cancelled_at',
        'cancellation_reason',
        'signed_by_student_at',
        'signed_by_organization_at',
        'signed_by_administration_at',
        'verification_document_url',
    ];

    protected $casts = [
        'assigned_department' => Enums\Department::class,
        'status' => Enums\Status::class,
        'currency' => Enums\Currency::class,
        'starting_at' => 'date',
        'ending_at' => 'date',
        'remuneration' => 'decimal:2',
        'signed_by_student_at' => 'datetime',
        'signed_by_organization_at' => 'datetime',
        'signed_by_administration_at' => 'datetime',
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

        static::addGlobalScope(new Scopes\ApprenticeshipScope);
        static::creating(function (Apprenticeship $apprenticeship) {
            $apprenticeship->student_id = auth()->id();
            $apprenticeship->year_id = Year::current()->id;
            $apprenticeship->status = Enums\Status::Announced;
            $apprenticeship->announced_at = now();
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
        return $this->belongsTo(InternshipAgreementContact::class, 'parrain_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'supervisor_id', 'id');
    }

    public function getDurationInWeeksAttribute()
    {
        // if starting at is carbon instance

        if ($this->starting_at instanceof Carbon && $this->ending_at instanceof Carbon) {
            // return $this->starting_at->diffInWeeks($this->ending_at);
            return ceil($this->starting_at->floatDiffInRealWeeks($this->ending_at));            // return $this->starting_at->longAbsoluteDiffForHumans($this->ending_at);

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

    public function applyForCancellation($reason, $verificationDocumentUrl)
    {
        $this->status = Enums\Status::PendingCancellation;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->verification_document_url = $verificationDocumentUrl;
        $this->save();
    }

    public function appliedCancellation()
    {
        return $this->status === Enums\Status::PendingCancellation;
    }

    public function cancel()
    {
        $this->status = Enums\Status::Rejected;
        $this->cancelled_at = now();
        $this->save();
    }

    public function getCurrentApprenticeshipAttribute()
    {
        return $this->where('student_id', auth()->id())
            ->where('status', Enums\Status::Announced)
            ->orWhere('status', Enums\Status::Validated)
            ->first();
    }

    public function getAgreementPdfUrlAttribute()
    {
        // example of variables : path:"storage/pdf/apprenticeship_agreements/FirstYear"	filename:"convention-de-stage-aya-hichabe-1714397022.pdf"
        return asset($this->pdf_path) . '/' . $this->pdf_file_name;
    }
}
