<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ApprenticeshipAmendment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'apprenticeship_id',
        'title',
        'description',
        'new_starting_at',
        'new_ending_at',
        'status',
        'reason',
        'validated_at',
        'rejected_at',
        'validated_by',
        'validation_comment',
    ];
    
    // protected static function booted(): void
    // {
    //     // Validate that amendment period doesn't exceed 8 weeks
    //     static::saving(function (ApprenticeshipAmendment $amendment) {
    //         if ($amendment->new_starting_at && $amendment->new_ending_at) {
    //             $weeks = ceil($amendment->new_starting_at->floatDiffInRealWeeks($amendment->new_ending_at));
    //             if ($weeks > 8) {
    //                 throw new \Exception('The amended internship period cannot exceed 8 weeks.');
    //             }
    //         }
    //     });
    // }
    
    protected $casts = [
        'new_starting_at' => 'date',
        'new_ending_at' => 'date',
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
    
    protected $appends = [
        'internship_period',
    ];
    
    /**
     * Get the apprenticeship that owns the amendment.
     */
    public function apprenticeship()
    {
        return $this->belongsTo(Apprenticeship::class);
    }
    
    /**
     * Get the administrator who validated the amendment.
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
    
    /**
     * Get a formatted internship period.
     */
    public function getInternshipPeriodAttribute()
    {
        if ($this->new_starting_at && $this->new_ending_at) {
            return $this->new_starting_at->format('d/m/Y') . ' - ' . $this->new_ending_at->format('d/m/Y');
        }
        return null;
    }
    
    /**
     * Set the internship period from a formatted string.
     */
    public function setInternshipPeriodAttribute($value)
    {
        if (strpos($value, ' - ') !== false) {
            [$start, $end] = explode(' - ', $value);
            $this->attributes['new_starting_at'] = Carbon::createFromFormat('d/m/Y', $start);
            $this->attributes['new_ending_at'] = Carbon::createFromFormat('d/m/Y', $end);
        }
    }
    
    /**
     * Check if the amendment modifies title/description.
     */
    public function modifiesDetails()
    {
        return !empty($this->title) || !empty($this->description);
    }
    
    /**
     * Check if the amendment modifies internship period.
     */
    public function modifiesPeriod()
    {
        return $this->new_starting_at !== null || $this->new_ending_at !== null;
    }
}
