<?php

namespace App\Filament\Administration\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';

    protected static ?string $navigationGroup = 'Internships';

    protected static ?string $title = 'Calendrier des sorties en stage';
}
