<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DisplaySettings extends Settings
{
    public bool $display_plannings;
    public bool $display_project_reviewers;

    public static function group(): string
    {
        return 'display';
    }
}