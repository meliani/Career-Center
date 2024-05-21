<?php

namespace App\Services;

class EncryptionService
{
    public static function encapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $encapsulated = openssl_encrypt($url, 'AES-256-CBC', $secretKey, 0, $iv);

        return base64_encode($encapsulated);
    }

    public static function decapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $decapsulated = openssl_decrypt(base64_decode($url), 'AES-256-CBC', $secretKey, 0, $iv);

        return $decapsulated;
    }
}
