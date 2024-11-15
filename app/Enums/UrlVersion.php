<?php

namespace App\Enums;

use App\Enums\Concerns\HasBaseEnumFeatures;

enum UrlVersion: string
{
    use HasBaseEnumFeatures;
    
    case V1 = 'v1';
    case V2 = 'v2';
    case V3 = 'v3';
}
