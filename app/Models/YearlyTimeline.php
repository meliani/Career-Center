<?php

namespace App\Models;

use App\Enums\TimelineCategory;
use App\Enums\TimelinePriority;
use App\Enums\TimelineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class YearlyTimeline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'color',
        'icon',
        'category',
        'priority',
        'status',
        'year_id',
        'is_highlight',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'category' => TimelineCategory::class,
        'priority' => TimelinePriority::class,
        'status' => TimelineStatus::class,
        'is_highlight' => 'boolean',
    ];

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('year_id', Year::current()->id);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'user_yearly_timeline');
    }
}
