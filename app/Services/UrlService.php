<?php

namespace App\Services;

use App\Enums;
use Illuminate\Support\Facades\Crypt;

class UrlService
{
    protected static $verification_string = '';

    protected static $separator = '?/$';

    private static function getVersion($url)
    {
        $parts = explode(self::$separator, $url, 2);

        if (count($parts) < 3) {
            throw new \Exception('Invalid data');
        }

        [$version, $url] = $parts;

        return $version;
    }

    private static function encryptv1($verification_string)
    {
        $encrypted_x = Enums\UrlVersion::V1->value . self::$separator . Crypt::encryptString($verification_string);

        return $encrypted_x;
    }

    private static function decryptv1($cipher)
    {
        $parts = explode(self::$separator, $cipher, 2);

        if (count($parts) < 2) {
            throw new \Exception('Invalid encrypted data format');
        }

        [$version, $verification_string] = $parts;
        $verification_string = Crypt::decryptString($verification_string);

        [$StudentId, $ApprenticeshipId] = explode('-', $verification_string);

        return ['StudentId' => $StudentId, 'ApprenticeshipId' => $ApprenticeshipId];
    }

    private static function encapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $encapsulated = openssl_encrypt($url, 'AES-256-CBC', $secretKey, 0, $iv);

        return base64_encode($encapsulated);
    }

    private static function decapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $decapsulated = openssl_decrypt(base64_decode($url), 'AES-256-CBC', $secretKey, 0, $iv);

        return $decapsulated;
    }

    public static function encodeUrl($verification_string)
    {
        return self::encapsulate(self::encryptv1($verification_string));
    }

    public static function decodeUrl($url)
    {
        return self::decryptv1(self::decapsulate($url));
    }
}
