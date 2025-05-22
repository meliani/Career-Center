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
use Filament\Notifications\Notification;

class Apprenticeship extends Model
{
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected $table = 'apprenticeships';

    protected $appends = [
        'duration_in_weeks',
        'encoded_url',
        'decoded_url',
        'internship_period',
    ];

    protected $fillable = [
        'student_id',
        'year_id',
        'project_id',
        'status',
        'internship_level',
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
        'internship_type',
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
        'verification_string',
    ];

    protected $casts = [
        'assigned_department' => Enums\Department::class,
        'status' => Enums\Status::class,
        'currency' => Enums\Currency::class,
        'internship_level' => Enums\InternshipLevel::class,
        'internship_type' => Enums\InternshipType::class,
        'starting_at' => 'date',
        'ending_at' => 'date',
        'remuneration' => 'decimal:2',
        'signed_by_student_at' => 'datetime',
        'signed_by_organization_at' => 'datetime',
        'signed_by_administration_at' => 'datetime',
        'tags' => 'array',
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
            
            // Automatically assign internship level based on student level
            if (auth()->user() instanceof Student) {
                $studentLevel = auth()->user()->level;
                
                if ($studentLevel === Enums\StudentLevel::FirstYear) {
                    $apprenticeship->internship_level = Enums\InternshipLevel::IntroductoryInternship;
                } elseif ($studentLevel === Enums\StudentLevel::SecondYear) {
                    $apprenticeship->internship_level = Enums\InternshipLevel::TechnicalInternship;
                } elseif ($studentLevel === Enums\StudentLevel::ThirdYear) {
                    $apprenticeship->internship_level = Enums\InternshipLevel::FinalYearInternship;
                }
            }
        });
        
        // Validate internship period on both create and update operations
        static::saving(function (Apprenticeship $apprenticeship) {
            if ($apprenticeship->starting_at && $apprenticeship->ending_at) {
                // Check if apprenticeship starts before May 15th
                $validStartDate = Carbon::create(null, 5, 15); // May 15th of current year
                if ($apprenticeship->starting_at->year > $validStartDate->year) {
                    $validStartDate->setYear($apprenticeship->starting_at->year);
                } elseif ($apprenticeship->starting_at->year < $validStartDate->year) {
                    $validStartDate->subYear();
                }
                
                // Check if apprenticeship ends after July 31st
                $validEndDate = Carbon::create(null, 7, 31); // July 31st of current year
                if ($apprenticeship->ending_at->year > $validEndDate->year) {
                    $validEndDate->setYear($apprenticeship->ending_at->year);
                } elseif ($apprenticeship->ending_at->year < $validEndDate->year) {
                    $validEndDate->subYear();
                }
                
                // Validate dates
                if ($apprenticeship->starting_at->lt($validStartDate) && !app()->runningInConsole()) {
                    Notification::make()
                        ->title('Invalid Start Date')
                        ->body('The apprenticeship cannot start before May 15th.')
                        ->danger()
                        ->send();
                        
                    return false; // Prevent saving the model
                }
                
                // Enforce July 31st end date for everyone, including administrators
                if ($apprenticeship->ending_at->gt($validEndDate)) {
                    // Only bypass for console operations (e.g., seeding)
                    if (app()->runningInConsole()) {
                        \Illuminate\Support\Facades\Log::info('Console operation bypassing end date validation', [
                            'apprenticeship_id' => $apprenticeship->id,
                            'ending_at' => $apprenticeship->ending_at->format('Y-m-d'),
                            'valid_end_date' => $validEndDate->format('Y-m-d')
                        ]);
                    } else {
                        // Log the validation event
                        \Illuminate\Support\Facades\Log::debug('End date validation triggered', [
                            'user_id' => auth()->id(),
                            'user_type' => auth()->user() ? get_class(auth()->user()) : 'unauthenticated',
                            'is_admin' => auth()->check() && auth()->user()->isAdministrator(),
                            'apprenticeship_id' => $apprenticeship->id,
                            'ending_at' => $apprenticeship->ending_at->format('Y-m-d'),
                            'valid_end_date' => $validEndDate->format('Y-m-d')
                        ]);
                        
                        Notification::make()
                            ->title('Invalid End Date')
                            ->body('The apprenticeship cannot end after July 31st.')
                            ->danger()
                            ->send();
                            
                        return false; // Prevent saving the model for all users
                    }
                }
                
                $weeks = ceil($apprenticeship->starting_at->floatDiffInRealWeeks($apprenticeship->ending_at));
                if ($weeks > 8 && (auth()->user() instanceof Student)) {
                    Notification::make()
                        ->title('Internship Period Exceeded')
                        ->body('The internship period cannot exceed 8 weeks.')
                        ->danger()
                        ->send();

                        return false; // Prevent saving the model
                    //throw new \Exception('The internship period cannot exceed 8 weeks.');
                } elseif ($weeks > 8 && (auth()->user() instanceof User)) {
                    // Only enforce 8-week restriction if not being saved from administration panel
                    $isAdministrator = auth()->check() && auth()->user()->isAdministrator();
                    $currentPath = request()->path();
                    $isBackendUrl = request()->is('*/backend/*');
                    
                    $isAdminOperation = $isAdministrator;
                    
                    // Log route information for debugging if needed
                    if ($weeks > 8 && $isAdministrator) {
                        \Illuminate\Support\Facades\Log::debug('Apprenticeship 8-week validation triggered', [
                            'current_path' => $currentPath,
                            'is_admin' => $isAdministrator,
                            'is_backend_url' => $isBackendUrl,
                            'admin_operation_bypass' => $isAdminOperation,
                            'weeks' => $weeks
                        ]);
                    }
                    
                    if ($weeks > 8 && !$isAdminOperation) {
                        throw new \Exception('The internship period cannot exceed 8 weeks.');
                    }
                }
            }
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

    /**
     * Generate a verification link for this apprenticeship agreement
     * 
     * @return string The verification URL
     */
    public function generateVerificationLink()
    {
        $verification_string = \App\Services\UrlService::encodeShortUrl($this->attributes[env('INTERNSHIPS_ENCRYPTION_FIELD', 'id')]);
        $verification_url = route('internship-agreement.verify', $verification_string);

        $this->verification_string = $verification_string;
        $this->save();

        return $verification_url;
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
    
    /**
     * Get the amendments for the apprenticeship.
     */
    public function amendments()
    {
        return $this->hasMany(ApprenticeshipAmendment::class);
    }
    
    /**
     * Check if the apprenticeship has any pending amendments.
     */
    public function hasPendingAmendmentRequests(): bool
    {
        return $this->amendments()->where('status', 'pending')->exists();
    }
    
    /**
     * Get pending amendments relationship - used for Filament filtering.
     */
    public function pendingAmendments()
    {
        return $this->amendments()->where('status', 'pending');
    }
    
    /**
     * Apply an amendment that has been validated.
     */
    public function applyAmendment(ApprenticeshipAmendment $amendment)
    {
        if ($amendment->status === 'validated') {
            if (!empty($amendment->title)) {
                $this->title = $amendment->title;
            }
            
            if (!empty($amendment->description)) {
                $this->description = $amendment->description;
            }
            
            if ($amendment->new_starting_at) {
                $this->starting_at = $amendment->new_starting_at;
            }
            
            if ($amendment->new_ending_at) {
                $this->ending_at = $amendment->new_ending_at;
            }
            
            $this->save();
        }
    }
}
