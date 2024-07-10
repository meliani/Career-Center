<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use Illuminate\Http\Request;

class DiplomaVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code)
    {
        $verification_code = \App\Services\UrlService::decodeDiplomaUrl($verification_code);
        // dd($id);
        $payload = Diploma::where('registration_number', $verification_code)->first();

        if (! $payload) {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => false,
                ]
            );
        } else {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                ]
            );
        }
    }
}
