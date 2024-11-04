<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateUserLastLoginAt
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $user->update([
            'last_login_at' => now(),
        ]);
    }
}
