<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use Illuminate\Http\Request;

class DiplomaVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code)
    {
        $registration_number = \App\Services\UrlService::decodeShortUrl($verification_code);
        // dd($id);
        $payload = Diploma::where('registration_number', $registration_number)->first();

        if (! $payload) {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => false,
                    'verification_code' => $verification_code,
                ]
            );
        } else {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                    'verification_code' => $verification_code,
                ]
            );
        }
    }
}
