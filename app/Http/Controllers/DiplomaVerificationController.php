<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use Illuminate\Http\Request;

class DiplomaVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code = null)
    {
        // dd($verification_code);
        if (! $verification_code) {
            return view('filament.org.pages.diploma-verification-response', [
                'payload' => null,
                'is_authentic' => false,
                'verification_code' => $verification_code,
                'message' => __('QR code is empty. Please don\'t try again.'),
            ]);
        }
        $encrypted_field = \App\Services\UrlService::decodeShortUrl($verification_code);

        // dd($id);
        $payload = Diploma::where(env('ENCRYPTED_FIELD', 'hey'), $encrypted_field)->first();
        $payload = Diploma::where(env('VERIFICATION_FIELD', 'hey'), $verification_code)
            ->orWhere(env('VERSO_VERIFICATION_FIELD', 'hey'), $verification_code)
            ->first();
        if (! $payload) {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => false,
                    'verification_code' => $verification_code,
                    'message' => __('This QR code is invalid. Please try again.'),
                ]
            );
        } else {
            return view(
                'filament.org.pages.diploma-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                    'verification_code' => $verification_code,
                    'message' => __('Diploma information'),
                ]
            );
        }
    }
}
