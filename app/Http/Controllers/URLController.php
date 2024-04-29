<?php

namespace App\Http\Controllers;

use App\Enums;
use App\Models\Apprenticeship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class URLController extends Controller
{
    protected $verification_string;

    protected $separator = '?/$';

    public function __invoke(Request $request)
    {

        $this->verification_string = Apprenticeship::first()->verification_string;
        // dd($agreement->verification_string);

        $x = $request->get('x');

        return $this->InternalRedirect($x);
    }

    public function InternalRedirect($url)
    {
        // dd($this->encapsulate($this->encryptv1($this->verification_string)));

        dd($this->getVersion($this->decapsulate($url)));

        // $userId = $this->decryptv1($url);

        // dd($userId);

        return redirect($url);

    }

    private function getVersion($url)
    {
        // dd($url);

        $parts = explode($this->separator, $url, 2);

        dd($parts, $url);

        if (count($parts) < 3) {
            throw new \Exception('Invalid data');
        }

        [$version, $userId, $url] = $parts;

        return $version;
    }

    private function encryptv1($verification_string)
    {
        $encrypted_x = Enums\UrlVersion::V1->value . $this->separator . Crypt::encryptString($this->verification_string);

        return $encrypted_x;
    }

    private function decryptv1($cipher)
    {
        $data = Crypt::decryptString($cipher);
        $parts = explode($this->separator, $data, 3);

        if (count($parts) < 3) {
            throw new \Exception('Invalid encrypted data format');
        }

        [$version, $userId, $url] = $parts;

        return ['version' => $version, 'userId' => $userId, 'url' => $url];
    }

    private function encapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $encapsulated = openssl_encrypt($url, 'AES-256-CBC', $secretKey, 0, $iv);

        return base64_encode($encapsulated);
    }

    private function decapsulate($url)
    {
        $secretKey = env('APP_KEY');
        $iv = substr(hash('sha256', env('APP_IV')), 0, 16);
        $decapsulated = openssl_decrypt(base64_decode($url), 'AES-256-CBC', $secretKey, 0, $iv);

        return $decapsulated;
    }
}
