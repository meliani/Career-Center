<?php

namespace App\Http\Controllers;

use App\Models\DeliberationPV;
use Illuminate\Http\Request;

class PVVerificationController extends Controller
{
    public function __invoke(Request $request, $verification_code)
    {
        $verification_code = \App\Services\UrlService::decodeShortUrl($verification_code);
        // dd($id);
        $payload = DeliberationPV::where('id', $verification_code)->first();

        if (! $payload) {
            $payload = DeliberationPV::first();

            return view(
                'filament.org.pages.deliberation-pv-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                ]
            );
        } else {
            return view(
                'filament.org.pages.deliberation-pv-verification-response',
                [
                    'payload' => $payload,
                    'is_authentic' => true,
                ]
            );
        }
    }
}
