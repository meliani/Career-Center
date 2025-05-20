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
