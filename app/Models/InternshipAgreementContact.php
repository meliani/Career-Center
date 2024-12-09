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

    public static function createOrUpdate(array $attributes)
    {
        $mainFields = ['first_name', 'last_name', 'email', 'organization_id'];

        $query = self::query();
        foreach ($mainFields as $field) {
            if (isset($attributes[$field])) {
                $query->where($field, $attributes[$field]);
            }
        }

        $existingContact = $query->first();

        if ($existingContact) {
            // Calculate completeness scores
            $existingScore = collect($existingContact->getAttributes())
                ->except(['id', 'created_at', 'updated_at', 'deleted_at'])
                ->filter()
                ->count();

            $newScore = collect($attributes)
                ->except(['id', 'created_at', 'updated_at', 'deleted_at'])
                ->filter()
                ->count();

            // Merge attributes, preferring:
            // 1. Non-null values
            // 2. Newer record's value if both are filled
            // 3. Keeping the earliest created_at
            $mergedAttributes = collect($existingContact->getAttributes())
                ->except(['id', 'updated_at', 'deleted_at'])
                ->map(function ($value, $key) use ($attributes, $newScore, $existingScore) {
                    // Skip if key doesn't exist in new attributes
                    if (! array_key_exists($key, $attributes)) {
                        return $value;
                    }

                    $newValue = $attributes[$key];

                    // Special handling for created_at
                    if ($key === 'created_at') {
                        return min($value, $attributes[$key] ?? $value);
                    }

                    // If one value is null, take the non-null value
                    if ($value === null) {
                        return $newValue;
                    }
                    if ($newValue === null) {
                        return $value;
                    }

                    // If both values are different, prefer the record with higher completeness
                    if ($value !== $newValue) {
                        return $newScore > $existingScore ? $newValue : $value;
                    }

                    return $value;
                })
                ->toArray();

            $existingContact->fill($mergedAttributes);
            if ($existingContact->isDirty()) {
                $existingContact->save();
            }

            return $existingContact;
        }

        return self::create($attributes);
    }
}
