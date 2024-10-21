<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getTitle(): string | Htmlable
    {
        return __('Career Center - Student Login');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Student Login');
    }
}
