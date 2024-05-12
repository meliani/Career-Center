<?php

namespace App\Filament\Administration\Pages;

use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageNotification extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = NotificationSettings::class;

    protected static ?string $title = 'Notification Settings';

    protected static ?string $navigationLabel = 'Notification Settings';

    protected static ?string $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __(self::$navigationLabel);
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public function getTitle(): string
    {
        return __(self::$title);
    }

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator();
        } else {
            return false;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('in_app')
                    ->label('Enable In-App Notifications'),
                Forms\Components\Toggle::make('by_email')
                    ->label('Enable Email Notifications'),
                Forms\Components\Toggle::make('by_sms')
                    ->label('Enable SMS Notifications')
                    ->disabled(),
            ]);
    }
}
