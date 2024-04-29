<?php

namespace App\Http\Controllers;

use App\Filament\App\Pages;
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

        dd(Pages\QrResponse::getUrl(panel: 'app'));

        return redirect(Pages\QrResponse::getUrl());
        // \App\Filament\Administration\Resources\ProjectResource::getUrl('edit', [$record->id])
    }
}
