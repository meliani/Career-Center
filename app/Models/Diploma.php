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
        'defense_status',
        'is_foreign',
        'is_deliberated',
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

    public static function syncWithDefenses()
    {
        $diplomas = Diploma::all();
        $students = \App\Models\Student::get('first_name', 'last_name', 'defense_status');
        $students = \App\Models\Student::whereHas('internship', function ($query) {
            $query->whereHas('project');
        })
            ->get();
        // ->get('name', 'first_name', 'last_name', 'defense_status');
        // Stats about the distances between the names of the students and the diplomas
        //  VAR = 12.29
        // AVG = 15.59
        // MAX = 30
        // MIN = 3

        foreach ($diplomas as $diploma) {
            foreach ($students as $student) {
                $distances = levenshtein($student->name, $diploma->full_name);
                if ($distances < 12) {
                    $diploma->update(['defense_status' => $student->internship->project->defense_status]);
                }
            }
        }
    }
}
