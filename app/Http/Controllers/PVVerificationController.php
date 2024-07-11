<?php

namespace App\Http\Controllers;

use App\Models\DeliberationPV;
use Illuminate\Http\Request;

class PVVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code)
    {
        // $verification_code = \App\Services\UrlService::decodeShortUrl($verification_code);
        // dd($id);
        $payload = DeliberationPV::where('verification_string', $verification_code)->first();
        // dd($verification_code);

        if (! $payload) {
            // $payload = DeliberationPV::first();
            // \App\Models\LinkVerification::recordScan($request->url(), $verification_code, false, $request->ip(), $request->userAgent());

            return view(
                'filament.org.pages.deliberation-pv-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => false,
                    'verification_code' => $verification_code,
                ]
            );
        } else {

            return view(
                'filament.org.pages.deliberation-pv-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                    'verification_code' => $verification_code,
                ]
            );
        }
    }
}
