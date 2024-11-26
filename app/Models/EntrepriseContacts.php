<?php

namespace App\Models;

use App\Enums\EntrepriseContactCategory;
use App\Enums\Title;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrepriseContacts extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'last_time_contacted',
        'last_year_id_supervised',
        'first_year_id_supervised',
        'interactions_count',
    ];

    protected $casts = [
        'is_account_disabled' => 'boolean',
        'category' => EntrepriseContactCategory::class,
        'title' => Title::class,
        'last_time_contacted' => 'datetime',

    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getLongFullNameAttribute(): string
    {
        return trim("{$this->title?->getLongTitle()} {$this->first_name} {$this->last_name}");
    }

    // year relationship

    public function year()
    {
        return $this->belongsTo(Year::class, 'last_year_id_supervised', 'id');
    }

    public function trackInteraction(): void
    {
        $this->last_time_contacted = now();
        $this->interactions_count = ($this->interactions_count ?? 0) + 1;
        $this->save();
    }
}
