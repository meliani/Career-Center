<?php

namespace App\Http\Controllers;

use App\Models\FinalYearInternshipAgreement;
use App\Models\MasterResearchInternshipAgreement;
use Illuminate\Http\Request;

class AgreementVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code = null)
    {

        if (! $verification_code) {
            return view('filament.org.pages.internship-agreement-verification-response', [
                'payload' => null,
                'is_authentic' => false,
                'verification_code' => $verification_code,
                'message' => __('QR code is empty. Please don\'t try again.'),
            ]);
        }
        // $encrypted_field = \App\Services\UrlService::decodeShortUrl($verification_code);

        // $payload = FinalYearInternshipAgreement::where(env('INTERNSHIPS_ENCRYPTION_FIELD', 'hey'), $encrypted_field)->first();
        $payload = FinalYearInternshipAgreement::where(env('INTERNSHIPS_VERIFICATION_FIELD', 'hey'), $verification_code)->first();

        if (! $payload) {
            $payload = MasterResearchInternshipAgreement::where('verification_string', $verification_code)->first();

            // dd(MasterResearchInternshipAgreement::all());
        }
        if (! $payload) {
            return view(
                'filament.org.pages.internship-agreement-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => false,
                    'verification_code' => $verification_code,
                    'message' => __('This QR code is invalid. Please try again.'),
                ]
            );
        } else {
            return view(
                'filament.org.pages.internship-agreement-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                    'verification_code' => $verification_code,
                    'message' => __('Internship Agreement information'),
                ]
            );
        }
    }
}
