<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidTermReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'project_id',
        'submitted_at',
        'is_read_by_supervisor',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_read_by_supervisor' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
