<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentShareToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'student_ids', 'expires_at', 'filter_cv'];
    
    protected $casts = [
        'student_ids' => 'array',
        'expires_at' => 'datetime',
        'filter_cv' => 'boolean',
    ];
    
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
}
