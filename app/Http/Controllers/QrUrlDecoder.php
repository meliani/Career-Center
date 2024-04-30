<?php

namespace App\Http\Controllers;

use App\Filament\Org\Pages;
use App\Services\UrlService;
use Illuminate\Http\Request;

class QrUrlDecoder extends Controller
{
    protected $verification_string;

    protected $separator = '?/$';

    public function __invoke(Request $request)
    {

        // dd($agreement->verification_string);

        $x = $request->get('x');
        // dd($x, UrlService::decodeUrl($x));

        $Apprenticeship = UrlService::decodeUrl($x);

        $StudentId = $Apprenticeship['StudentId'];
        $ApprenticeshipId = $Apprenticeship['ApprenticeshipId'];

        // dd(Pages\QrResponse::getUrl(panel: 'org', parameters: ['StudentId' => $StudentId, 'ApprenticeshipId' => $ApprenticeshipId]));

        return view('filament.org.pages.qr-response', ['StudentId' => $StudentId, 'ApprenticeshipId' => $ApprenticeshipId]);
        // \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$record->id])
    }
}
