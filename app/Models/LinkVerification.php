<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkVerification extends Model
{
    protected $fillable = ['url', 'verification_string', 'is_valid', 'ip_address', 'user_agent'];

    public static function recordScan($url, $verificationString, $isValid, $ipAddress, $userAgent)
    {
        return self::create([
            'url' => $url,
            'verification_string' => $verificationString,
            'is_valid' => $isValid,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,

        ]);
    }
}
