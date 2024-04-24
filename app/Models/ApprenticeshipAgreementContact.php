<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprenticeshipAgreementContact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'apprenticeship_agreement_contacts';

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'function',
        'phone',
        'email',
        'role',
        'organization_id',
        'apprenticeship_id',
    ];

    protected $enumCasts = [
        'role' => Enums\OrganizationContactRole::class,
    ];

    protected $appends = [
        'full_name',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function apprenticeship()
    {
        return $this->belongsTo(Apprenticeship::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
