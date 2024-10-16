<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Auth\Login as BaseLogin;

class LoginStudent extends BaseLogin
{
    public ?array $data = [];

    public function mount(): void
    {
        dd('mount');
    }

    protected function getForms(): array
    {
        dd('getForms');
    }
}
