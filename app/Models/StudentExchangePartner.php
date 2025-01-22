<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentExchangePartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'city',
        'website',
        'email',
        'phone_number',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->city}, {$this->country}";
    }
}
