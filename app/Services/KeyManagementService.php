<?php

namespace App\Services;

class KeyManagementService
{
    public static function signData($data, $passphrase = null)
    {
        $privateKeyPath = 'file://' . config('app.private_key_path');
        $privateKey = openssl_pkey_get_private($privateKeyPath, $passphrase);

        if (! $privateKey) {
            throw new \Exception('Unable to load private key: ' . openssl_error_string());
        }

        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        return base64_encode($signature);
    }

    public static function verifySignature($data, $signature)
    {
        $publicKey = openssl_pkey_get_public('file://' . config('app.public_key_path'));
        $signature = base64_decode($signature);
        $isValid = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKey);

        return $isValid === 1;
    }
}
