<?php

namespace App\Filament\Org\Pages;

use Filament\Pages\Page;

class QrResponse extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.org.pages.qr-response';

    protected $StudentId;

    protected $ApprenticeshipId;

    // public function __construct($ApprenticeshipId = null, $StudentId = null)
    // {
    //     $this->StudentId = $StudentId;
    //     $this->ApprenticeshipId = $ApprenticeshipId;
    //     // dd($this->StudentId, $this->ApprenticeshipId);
    // }
}
