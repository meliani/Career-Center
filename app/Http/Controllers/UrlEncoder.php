<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;

class UrlEncoder extends Controller
{
    protected $verification_string;

    protected $separator = '?/$';

    public function __invoke(Request $request)
    {

        // dd($agreement->verification_string);

        $x = $request->get('x');

        return UrlService::encodeUrl($x);
    }
}
