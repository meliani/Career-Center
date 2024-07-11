<?php

namespace App\Models;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DeliberationPV extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_date',
        'attendees',
        'decisions',
        'remarks',
        'year_id',
        'qr_code',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'attendees' => 'array',
    ];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function generateVerificationQRCode()
    {
        $verification_string = \App\Services\UrlService::encodeShortUrl($this->id);
        $verification_url = route('deliberation-pv.verify', $verification_string);
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(500, 0, null, null, null),
                new SvgImageBackEnd()
            )
        ))->writeString($verification_url);
        $filename = 'qr-codes/' . $this->id . '.svg';
        Storage::disk('public')->put($filename, $svg);
        $this->qr_code = Storage::url($filename);

        $this->save();

        return $this->qr_code;
    }
}
