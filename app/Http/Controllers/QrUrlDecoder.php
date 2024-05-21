<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;

class QrUrlDecoder extends Controller
{
    public function __invoke(Request $request)
    {
        $x = $request->get('x');
        $Internship = UrlService::decodeUrl($x);

        // Exemple pour afficher les informations de l'étudiant et du stage
        $studentId = $Internship['StudentId'];
        $internshipId = $Internship['InternshipId'];

        return view('filament.org.pages.qr-response', [
            'slot' => 'qr-response',
            'studentId' => $studentId,
            'internshipId' => $internshipId,
        ]);
    }
}
