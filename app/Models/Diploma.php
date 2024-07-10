<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    // use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'registration_number',
        'cne',
        'cin',
        'first_name',
        'last_name',
        'full_name',
        'last_name_ar',
        'first_name_ar',
        'birth_place_ar',
        'birth_place_fr',
        'birth_date',
        'nationality',
        'council',
        'program_code',
        'assigned_program',
        'program_tifinagh',
        'program_english',
        'program_arabic',
        'qr_code',
    ];

    // protected $casts = [
    //     // 'birth_date' => 'date',
    //     // 'created_at' => 'datetime',
    //     // 'updated_at' => 'datetime',
    // ];

    public function getFullNameArAttribute()
    {
        return $this->last_name_ar . ' ' . $this->first_name_ar;
    }

    public function generateVerificationLink()
    {
        $verification_string = \App\Services\UrlService::encodeDiplomaUrl($this->registration_number);

        return route('diploma.verify', $verification_string);
    }
}
