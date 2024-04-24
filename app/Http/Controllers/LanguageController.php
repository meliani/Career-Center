<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        App::setLocale($lang);
        Session::put('applocale', $lang);

        return redirect()->back();

        // App::setLocale($lang);

        // return redirect()->back();
    }
}
