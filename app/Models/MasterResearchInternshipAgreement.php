<?php

namespace App\Models;

use App\Enums;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Spatie\Tags\HasTags;

class MasterResearchInternshipAgreement extends Model implements Agreement
{
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    // protected $table = 'final_year_internships';

    protected $fillable = [
        'student_id',
        'year_id',
        'status',
        'announced_at',
        'validated_at',
        'assigned_department',
        'department_assigned_at',
        'department_assigned_by',
        'received_at',
        'signed_at',
        'observations',
        'organization_id',
        'office_location',
        'title',
        'description',
        'starting_at',
        'ending_at',
        'remuneration',
        'currency',
        'workload',
        'parrain_id',
        'external_supervisor_id',
        'internal_supervisor_id',
        'pdf_path',
        'pdf_file_name',
        'cancelled_at',
        'cancellation_reason',
        'verification_document_url',
        'signed_by_student_at',
        'signed_by_organization_at',
        'signed_by_administration_at',
    ];

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
        'signed_by_student_at' => 'datetime',
        'signed_by_organization_at' => 'datetime',
        'signed_by_administration_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (MasterResearchInternshipAgreement $finalYearInternship) {
            $currentYear = Year::current();
            
            if (!$currentYear) {
                throw new \Exception('No current academic year is defined. Please contact an administrator.');
            }
            
            $finalYearInternship->student_id = auth()->id();
            $finalYearInternship->year_id = $currentYear->id;
            // $finalYearInternship->status = Enums\Status::Announced;
            $finalYearInternship->announced_at = now();

            // Check if student already has an agreement for this year
            $existingAgreement = self::where('student_id', $finalYearInternship->student_id)
                ->where('year_id', $finalYearInternship->year_id)
                ->first();

            if ($existingAgreement) {
                throw new \Exception('You already have a research internship agreement for this academic year.');
            }
        });

        static::updating(function (MasterResearchInternshipAgreement $finalYearInternship) {
            if ($finalYearInternship->isDirty('status')) {
                if ($finalYearInternship->status === Enums\Status::Announced) {
                    $finalYearInternship->announced_at = now();
                }
            }
        });

    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function project()
    {
        return $this->morphToMany(Project::class, 'agreeable', 'project_agreements')
            ->using(ProjectAgreement::class)
            ->withTimestamps();
    }

    public function getProjectAttribute()
    {
        return $this->project()->first();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function activeOrganizations()
    {
        return $this->belongsTo(Organization::class)->active();
    }

    public function parrain()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'parrain_id', 'id');
    }

    public function externalSupervisor()
    {
        return $this->belongsTo(InternshipAgreementContact::class, 'external_supervisor_id', 'id');
    }

    public function suggestedInternalSupervisor()
    {
        return $this->belongsTo(Professor::class, 'internal_supervisor_id', 'id');
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
        try {
            if ($this->starting_at && $this->ending_at) {
                return $this->starting_at->format('d/m/Y') . ' - ' . $this->ending_at->format('d/m/Y');
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error: ' . $e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    public function applyForCancellation($reason, $verificationDocumentUrl)
    {
        $this->status = Enums\Status::PendingCancellation;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->verification_document_url = $verificationDocumentUrl;
        $this->save();
    }

    public function generateVerificationLink()
    {

        $verification_string = \App\Services\UrlService::encodeShortUrl($this->attributes[env('INTERNSHIPS_ENCRYPTION_FIELD', 'id')]);
        $verification_url = route('internship-agreement.verify', $verification_string);

        $this->verification_string = $verification_string;
        $this->save();

        return $verification_url;
    }

    public function getAgreementPdfUrlAttribute()
    {
        return asset($this->pdf_path) . '/' . $this->pdf_file_name;
    }

    public function appliedCancellation()
    {
        return $this->status === Enums\Status::PendingCancellation;
    }

    public function validate(?string $department = null)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException;
            }

            $this->validated_at = now();
            $this->status = Enums\Status::Validated;
            $this->assigned_department = $department;
            $this->save();
            \Filament\Notifications\Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            \Filament\Notifications\Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function sign()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException;
            }
            $this->signed_at = now();
            $this->status = Enums\Status::Signed;
            $this->save();
            \Filament\Notifications\Notification::make()
                ->title('Signed successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            \Filament\Notifications\Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function receive()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException;
            }
            $this->received_at = now();
            $this->status = Enums\Status::Completed;
            $this->save();
            \Filament\Notifications\Notification::make()
                ->title('Achieved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            \Filament\Notifications\Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function assignDepartment($department)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException;
            }
            $this->assigned_department = $department;
            $this->department_assigned_at = now();
            $this->department_assigned_by = auth()->id();
            $this->save();
            \Filament\Notifications\Notification::make()
                ->title('Assigned successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            \Filament\Notifications\Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    // Implement Agreement Interface methods
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAssignedDepartment(): ?string
    {
        return $this->assigned_department->value ?? null;
    }

    public function getOrganizationName(): string
    {
        return $this->organization->name ?? 'Undefined Organization';
    }

    public function getStudentName(): string
    {
        return $this->student->full_name;
    }

    public function getStartDate(): ?\Carbon\Carbon
    {
        return $this->starting_at;
    }

    public function getEndDate(): ?\Carbon\Carbon
    {
        return $this->ending_at;
    }
}
