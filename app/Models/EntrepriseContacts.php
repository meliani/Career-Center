<?php

namespace App\Models;

use App\Enums\EntrepriseContactCategory;
use App\Enums\Title;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrepriseContacts extends Model
{
    use HasFactory;

    protected $table = 'entreprise_contacts';

    protected $fillable = [
        'email',
        'title',
        'first_name',
        'last_name',
        'company',
        'position',
        'alumni_promotion',
        'category',
        'years_of_interactions_with_students',
        'number_of_bounces',
        'bounce_reason',
        'is_account_disabled',
    ];

    protected $casts = [
        'is_account_disabled' => 'boolean',
        'category' => EntrepriseContactCategory::class,
        'title' => Title::class,
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getLongFullNameAttribute(): string
    {
        return trim("{$this->title->getLabel()} {$this->first_name} {$this->last_name}");
    }
}
