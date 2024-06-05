<?php

namespace App\Http\Controllers;

use App\Mail\SecondYearCampaign;
use Illuminate\Support\Str;

class MailPreviewController extends Controller
{
    public function show($mailableName)
    {
        $mailableClass = 'App\\Mail\\' . Str::studly($mailableName);

        if (! class_exists($mailableClass)) {
            abort(404, "The mailable {$mailableClass} does not exist.");
        }

        $mailable = new $mailableClass();

        // return new $mailable;

        return new SecondYearCampaign('John Doe');
    }
}
