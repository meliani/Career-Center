<?php

namespace App\Filament\Alumni\Pages;

use Filament\Pages\Page;

class MyProfilePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'my-profile';

    protected static string $view = 'filament-breezy::filament.pages.my-profile';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?string $title = 'Your updates';

    protected ?string $heading = 'Give us your updates';

    protected ?string $subheading = 'Please provide us with your latest information';

    public function getTitle(): string
    {
        // return __('filament-breezy::default.profile.my_profile');
        return __(self::$title);
    }

    public function getHeading(): string
    {
        // return __('filament-breezy::default.profile.my_profile');
        return __($this->heading) ?? null;

    }

    public function getSubheading(): ?string
    {
        // return __('filament-breezy::default.profile.subheading') ?? null;
        return __($this->subheading) ?? null;
    }

    public static function getSlug(): string
    {
        return filament('filament-breezy')->slug();
    }

    public static function getNavigationLabel(): string
    {
        // return __('filament-breezy::default.profile.profile');
        return self::$navigationLabel;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // return filament('filament-breezy')->shouldRegisterNavigation('myProfile');
        return true;
    }

    public static function getNavigationGroup(): ?string
    {
        return filament('filament-breezy')->getNavigationGroup('myProfile');
    }

    public function getRegisteredMyProfileComponents(): array
    {
        return filament('filament-breezy')->getRegisteredMyProfileComponents();
    }
}
