<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public string $in_app;

    public string $by_email;

    public string $by_sms;

    public static function group(): string
    {
        return 'NotificationSettings';
    }

    // public static function repository(): ?string
    // {
    //     return 'global_settings';
    // }
}
