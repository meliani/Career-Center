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
        'verification_string',
        'defense_status',
        'is_foreign',
        'is_deliberated',
        'deliberation1_desision',
        'deliberation2_desision',
        'deliberation3_desision',
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

        $verification_string = \App\Services\UrlService::encodeShortUrl($this->attributes[env('ENCRYPTED_FIELD', 'hey')]);
        $verification_url = route('diploma.verify', $verification_string);

        // $this->verification_string = $verification_string;
        $this->save();

        return $verification_url;
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
            $best_distance = PHP_INT_MAX; // Initialize with the maximum possible value
            $best_match = null; // To keep track of the student with the closest name

            foreach ($students as $student) {
                $studentName = preg_replace('/\s+/', ' ', trim(strtolower($student->name)));
                $diplomaName = preg_replace('/\s+/', ' ', trim(strtolower($diploma->full_name)));
                $current_distance = levenshtein($studentName, $diplomaName);
                if ($current_distance < $best_distance) {
                    $best_distance = $current_distance;
                    $best_match = $student; // Update the best match
                }
            }

            // If a sufficiently close match is found, update the diploma
            if ($best_distance < 3) {
                $imported_students[] = $best_match->name . ' => ' . $diploma->full_name . ' (' . $best_distance . ')';

                $diploma->defense_status = $best_match->internship->project->defense_status;
                $diploma->save();
            }
        }
        dd($imported_students);
    }

    public function generateTextForQrCode()
    {
        $text = $this->full_name . "\n";
        $text .= $this->cin . "\n";
        $text .= 'Promotion : ' . $this->council . "\n";

        return $text;
    }
}
