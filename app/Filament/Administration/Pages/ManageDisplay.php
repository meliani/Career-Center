<?php

namespace App\Filament\Administration\Pages;

use App\Settings\DisplaySettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageDisplay extends SettingsPage
{
        protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = DisplaySettings::class;

    protected static ?string $title = 'Display Settings';

    protected static ?string $navigationLabel = 'Display Settings';

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
                Forms\Components\Toggle::make('display_plannings')
                    ->label('Display Plannings')
                    ->default(true),
                Forms\Components\Toggle::make('display_project_reviewers')
                    ->label('Display Project Reviewers')
                    ->default(true),
            ]);
    }
}
