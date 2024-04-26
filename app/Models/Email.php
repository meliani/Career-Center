<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use RickDBCN\FilamentEmail\Models\Email as FilamentEmail;

/**
 * Email
 *
 * @property string $from
 * @property string $to
 * @property string $cc
 * @property string $bcc
 * @property string $subject
 * @property string $text_body
 * @property string $html_body
 * @property string $raw_body
 * @property string $sent_debug_info
 * @property Carbon|null $created_at
 */
class Email extends FilamentEmail
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isSuperAdministrator();
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isSuperAdministrator();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isSuperAdministrator();
    }
}
