<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class Year extends Core\BackendBaseModel
{
    public const CACHE_DURATION = 86400; // 24 hours

    protected const CACHE_KEY_PREFIX = 'years_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'is_current',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'is_current' => 'boolean',
    ];

    public function actual()
    {
        return 7;
    }

    public static function current()
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . 'current',
            self::CACHE_DURATION,
            fn () => self::where('is_current', true)->first()
        );
    }

    protected static function booted()
    {
        // Clear related caches when a year is updated
        static::updated(function ($year) {
            Cache::forget(self::CACHE_KEY_PREFIX . 'current');
            // Clear all select caches
            for ($i = 0; $i < 10; $i++) {
                Cache::forget(self::CACHE_KEY_PREFIX . "select_{$i}");
            }
        });

        static::created(function ($year) {
            Cache::forget(self::CACHE_KEY_PREFIX . 'current');
            // Clear all select caches
            for ($i = 0; $i < 10; $i++) {
                Cache::forget(self::CACHE_KEY_PREFIX . "select_{$i}");
            }
        });
    }

    public static function getYearsForSelect($number = 2)
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . "select_{$number}",
            self::CACHE_DURATION,
            function () use ($number) {
                $currentYearId = self::current()->id;

                return self::whereBetween('id', [
                    $currentYearId - $number - 1,
                    $currentYearId + 1,
                ])->pluck('title', 'id');
            }
        );
    }

    /**
     * Get the Internships for the Year.
     */
    public function internships()
    {
        return $this->belongsToMany(InternshipAgreement::class);
    }
}
