<?php

namespace App\Models;

use App\Enums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class FinalYearInternshipAgreement extends Model
{
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected $table = 'final_year_internships';

    protected $appends = [
        // 'duration_in_weeks',
        // 'verification_string',
        // 'encoded_url',
        // 'decoded_url',
        'internship_period',
    ];

    protected $casts = [
        'assigned_department' => Enums\Department::class,
        'status' => Enums\Status::class,
        'currency' => Enums\Currency::class,
        'starting_at' => 'date',
        'ending_at' => 'date',
        'remuneration' => 'decimal:2',
        'is_signed_by_student' => 'boolean',
        'is_signed_by_organization' => 'boolean',
        'is_signed_by_administration' => 'boolean',
        'signed_by_student_at' => 'datetime',
        'signed_by_organization_at' => 'datetime',
        'signed_by_administration_at' => 'datetime',
    ];

    protected static function booted(): void
    {

        static::creating(function (FinalYearInternshipAgreement $finalYearInternship) {
            $finalYearInternship->student_id = auth()->id();
            $finalYearInternship->year_id = Year::current()->id;
            $finalYearInternship->status = Enums\Status::Announced;
            $finalYearInternship->announced_at = now();
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
        return $this->belongsTo(ApprenticeshipAgreementContact::class, 'external_supervisor_id', 'id');
    }

    public function setInternshipPeriodAttribute($value)
    {
        if (strpos($value, ' - ') !== false) {
            [$start, $end] = explode(' - ', $value);
            $this->attributes['starting_at'] = Carbon::createFromFormat('d/m/Y', $start);
            $this->attributes['ending_at'] = Carbon::createFromFormat('d/m/Y', $end);
        }
    }

    public function getInternshipPeriodAttribute()
    {
        return $this->starting_at->format('d/m/Y') . ' - ' . $this->ending_at->format('d/m/Y');
    }
}
