<?php

namespace App\Http\Controllers;

use App\Models\Apprenticeship;
use App\Models\Student;
use App\Services\UrlService;
use Illuminate\Http\Request;

class QrUrlDecoder extends Controller
{
    public function __invoke(Request $request)
    {
        $x = $request->get('x');
        $InternshipHash = UrlService::decodeUrl($x);

        // Exemple pour afficher les informations de l'étudiant et du stage
        $studentId = $InternshipHash['StudentId'];
        $internshipId = $InternshipHash['InternshipId'];

        if (is_null($studentId) || is_null($internshipId)) {
            return view('filament.org.pages.qr-response', [
                'slot' => 'qr-response',
                'studentId' => null,
                'internshipId' => null,
                'is_authentic' => false,
            ]);
        }

        $student = Student::find($studentId);
        $internship = Apprenticeship::find($internshipId);

        return view('filament.org.pages.qr-response', [
            'slot' => 'qr-response',
            'student' => $student,
            'internship' => $internship,
            'is_authentic' => true,
            'verification_code' => $x,
        ]);
        // ->layout('components.layouts.public');
    }
}
