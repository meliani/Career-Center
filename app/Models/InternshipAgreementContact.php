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
        $query = self::query();

        // Case insensitive email match
        if (isset($attributes['email'])) {
            $query->where('email', 'LIKE', $attributes['email']);
        }

        // Name matching with various patterns
        if (isset($attributes['first_name'], $attributes['last_name'])) {
            $query->where(function ($q) use ($attributes) {
                // Exact match (case insensitive)
                $q->where(function ($q) use ($attributes) {
                    $q->whereRaw('LOWER(first_name) = ?', [strtolower($attributes['first_name'])])
                        ->whereRaw('LOWER(last_name) = ?', [strtolower($attributes['last_name'])]);
                })
                // Initial with last name (e.g., "J. Smith" matches "John Smith")
                    ->orWhere(function ($q) use ($attributes) {
                        $q->whereRaw('LOWER(LEFT(first_name, 1)) = ?', [strtolower(substr($attributes['first_name'], 0, 1))])
                            ->whereRaw('LOWER(last_name) = ?', [strtolower($attributes['last_name'])]);
                    })
                // Handle possible swapped first/last names
                    ->orWhere(function ($q) use ($attributes) {
                        $q->whereRaw('LOWER(first_name) = ?', [strtolower($attributes['last_name'])])
                            ->whereRaw('LOWER(last_name) = ?', [strtolower($attributes['first_name'])]);
                    });
            });
        }

        // Organization match
        if (isset($attributes['organization_id'])) {
            $query->where('organization_id', $attributes['organization_id']);
        }

        // Find best matching contact
        $existingContacts = $query->get();

        if ($existingContacts->isNotEmpty()) {
            // Score each contact for best match
            $scoredContacts = $existingContacts->map(function ($contact) use ($attributes) {
                $score = 0;

                // Email exact match (highest weight)
                if (isset($attributes['email']) && strtolower($contact->email) === strtolower($attributes['email'])) {
                    $score += 100;
                }

                // Name matching score
                if (isset($attributes['first_name'], $attributes['last_name'])) {
                    // Exact name match
                    if (strtolower($contact->first_name) === strtolower($attributes['first_name']) &&
                        strtolower($contact->last_name) === strtolower($attributes['last_name'])) {
                        $score += 50;
                    }
                    // Initial match
                    elseif (strtolower(substr($contact->first_name, 0, 1)) === strtolower(substr($attributes['first_name'], 0, 1)) &&
                            strtolower($contact->last_name) === strtolower($attributes['last_name'])) {
                        $score += 30;
                    }
                }

                // Phone number match (normalized)
                if (isset($attributes['phone']) && $contact->phone) {
                    $normalizedPhone1 = preg_replace('/[^0-9+]/', '', $contact->phone);
                    $normalizedPhone2 = preg_replace('/[^0-9+]/', '', $attributes['phone']);
                    if ($normalizedPhone1 === $normalizedPhone2) {
                        $score += 40;
                    }
                }

                // Organization match
                if (isset($attributes['organization_id']) && $contact->organization_id === $attributes['organization_id']) {
                    $score += 20;
                }

                // Data completeness score
                $score += collect($contact->getAttributes())
                    ->except(['id', 'created_at', 'updated_at', 'deleted_at'])
                    ->filter()
                    ->count() * 2;

                return [
                    'contact' => $contact,
                    'score' => $score,
                ];
            });

            // Get the contact with highest match score
            $bestMatch = $scoredContacts->sortByDesc('score')->first()['contact'];

            // Merge attributes, preferring more complete/newer data
            $mergedAttributes = collect($bestMatch->getAttributes())
                ->except(['id', 'updated_at', 'deleted_at'])
                ->map(function ($value, $key) use ($attributes) {
                    if (! array_key_exists($key, $attributes)) {
                        return $value;
                    }

                    // Special handling for created_at
                    if ($key === 'created_at') {
                        return min($value, $attributes[$key] ?? $value);
                    }

                    // Prefer non-null values
                    if ($value === null) {
                        return $attributes[$key];
                    }
                    if (! isset($attributes[$key]) || $attributes[$key] === null) {
                        return $value;
                    }

                    // For other fields, prefer the newer value
                    return $attributes[$key];
                })
                ->toArray();

            $bestMatch->fill($mergedAttributes);
            if ($bestMatch->isDirty()) {
                $bestMatch->save();
            }

            return $bestMatch;
        }

        return self::create($attributes);
    }
}
