<?php

namespace App\Services;

use App\Enums;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class UrlService
{
    protected static $verification_string = '';

    protected static $separator = '?/$';

    // Constant to use when null value is provided
    protected static $null_placeholder = 'NULL_VALUE_PLACEHOLDER';

    private static function getVersion($url)
    {
        $parts = explode(self::$separator, $url, 2);

        if (count($parts) < 2) {
            throw new \Exception('Invalid data');
        }

        [$version, $url] = $parts;

        return $version;
    }

    private static function encryptv1($verification_string)
    {
        if (is_null($verification_string)) {
            Log::warning('Null verification string provided to encryptv1', [
                'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? 'unknown'
            ]);
            $verification_string = self::$null_placeholder;
        }

        $encrypted_x = Enums\UrlVersion::V1->value . self::$separator . Crypt::encryptString($verification_string);

        return $encrypted_x;
    }

    private static function encryptV1Short($verification_string)
    {
        if (is_null($verification_string)) {
            Log::warning('Null verification string provided to encryptV1Short', [
                'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? 'unknown'
            ]);
            $verification_string = self::$null_placeholder;
        }
        
        $cipher = 'AES-128-CBC';
        // Decode the APP_KEY from base64 to get the raw key
        $key = base64_decode(env('APP_KEY'));

        // Ensure the key is the correct length for AES-128-CBC (16 bytes)
        if (strlen($key) > 16) {
            $key = substr($key, 0, 16);
        }

        // Generate a random IV for AES-128-CBC
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        // Encrypt the verification string
        $encrypted = openssl_encrypt($verification_string, $cipher, $key, 0, $iv);

        // Combine the IV with the encrypted data to make it possible to decrypt later
        $encrypted_x = base64_encode($iv . $encrypted);

        // Convert the base64-encoded string to a URL-safe version
        $encrypted_x = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted_x);

        return $encrypted_x;
    }

    private static function decryptV1Short($encrypted_x)
    {
        // if (is_null($encrypted_x)) {
        //     throw new \Exception('Encrypted string cannot be null');
        // }
        // if (strlen($encrypted_x) < 54 || is_null($encrypted_x)) {
        //     return 'INVALID URL';
        // }

        $cipher = 'AES-128-CBC';
        // Decode the APP_KEY from base64 to get the raw key
        $key = base64_decode(env('APP_KEY'));

        // Ensure the key is the correct length for AES-128-CBC (16 bytes)
        if (strlen($key) > 16) {
            $key = substr($key, 0, 16);
            // dd($key);
        }

        // Convert the URL-safe base64-encoded string back to its original form
        $encrypted_x = str_replace(['-', '_'], ['+', '/'], $encrypted_x);
        // Decode the combined IV and encrypted data
        $combined = base64_decode($encrypted_x);

        // Extract the IV and encrypted data
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($combined, 0, $ivLength);
        $encrypted = substr($combined, $ivLength);

        // Decrypt the data
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
        
        // Check if we got our null placeholder back
        if ($decrypted === self::$null_placeholder) {
            return null;
        }
        
        return $decrypted;
    }

    private static function decryptv1($cipher)
    {
        $parts = explode(self::$separator, $cipher, 2);

        if (count($parts) < 2) {
            // throw new \Exception('Invalid encrypted data format');
            return ['StudentId' => null, 'InternshipId' => null];
        }

        [$version, $verification_string] = $parts;
        $verification_string = Crypt::decryptString($verification_string);
        
        // Check if we got our null placeholder back
        if ($verification_string === self::$null_placeholder) {
            return ['StudentId' => null, 'InternshipId' => null];
        }
        
        $parts = explode('-', $verification_string);
        
        // Ensure we have both parts before unpacking
        if (count($parts) < 2) {
            return ['StudentId' => $parts[0] ?? null, 'InternshipId' => null];
        }
        
        [$StudentId, $InternshipId] = $parts;

        return ['StudentId' => $StudentId, 'InternshipId' => $InternshipId];
    }

    private static function encapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV', 'default_iv')), 0, 16); // Provide a default value for APP_IV to avoid null

        if (is_null($url)) {
            Log::warning('Null URL provided to encapsulate', [
                'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? 'unknown'
            ]);
            $url = self::$null_placeholder;
        }

        $encapsulated = openssl_encrypt($url, 'AES-256-CBC', $secretKey, 0, $iv);

        return base64_encode($encapsulated);
    }

    private static function decapsulate($x)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV', 'default_iv')), 0, 16); // Provide a default value for APP_IV to avoid null
        // dd($x);
        if (is_null($x)) {
            throw new \Exception('URL cannot be null');
        }

        $decapsulated = openssl_decrypt(base64_decode($x), 'AES-256-CBC', $secretKey, 0, $iv);
        // dd($decapsulated);

        return $decapsulated;
    }

    public static function encodeUrl($verification_string)
    {
        try {
            return self::encapsulate(self::encryptv1($verification_string));
        } catch (\Exception $e) {
            Log::error('Error encoding URL: ' . $e->getMessage(), [
                'verification_string' => $verification_string ? 'exists' : 'null'
            ]);
            return null;
        }
    }

    public static function encodeShortUrl($verification_string)
    {
        try {
            return self::encryptV1Short($verification_string);
        } catch (\Exception $e) {
            Log::error('Error encoding short URL: ' . $e->getMessage(), [
                'verification_string' => $verification_string ? 'exists' : 'null'
            ]);
            return null;
        }
    }

    public static function decodeShortUrl($url)
    {
        return self::decryptV1Short($url);
    }

    public static function decodeUrl($url)
    {
        return self::decryptv1(self::decapsulate($url));
    }

    public static function getQrCodeSvg(string $url)
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(120, 1, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($url);

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }
}
