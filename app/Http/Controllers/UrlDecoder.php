<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;

class UrlDecoder extends Controller
{
    protected $verification_string;

    protected $separator = '?/$';

    public function __invoke(Request $request)
    {

        // dd($agreement->verification_string);

        $x = $request->get('x');

        $decoded_url = UrlService::decodeUrl($x);
        dd($decoded_url);

    }
}
