<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class Apprenticeship extends Model
{
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected $table = 'apprenticeships';

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
        'starting_at' => 'datetime',
        'ending_at' => 'datetime',
        'remuneration' => 'decimal:2',
        'keywords' => 'array',

    ];

    protected static function booted(): void
    {

        static::creating(function (Apprenticeship $apprenticeship) {
            $apprenticeship->student_id = auth()->id();
            $apprenticeship->year_id = Year::current()->id;

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

    public function apprenticeshipAgreementContacts()
    {
        return $this->hasMany(ApprenticeshipAgreementContact::class);
    }

    public function parrain()
    {
        return $this->belongsTo(ApprenticeshipAgreementContact::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(ApprenticeshipAgreementContact::class);
    }
}