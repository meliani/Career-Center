<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipAgreementContact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'function',
        'phone',
        'email',
        'role',
        'organization_id',
    ];

    protected $casts = [
        'role' => Enums\OrganizationContactRole::class,
        'title' => Enums\Title::class,
    ];

    protected $appends = [
        'full_name',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFormalNameAttribute()
    {
        return "{$this->title->getLongTitle()} {$this->first_name} {$this->last_name}";
    }
}
