<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\Facades\App;
use Illuminate\Routing\UrlGenerator;
use Filament\Tables\Columns\TextColumn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (App::environment('production')) {
            $url->formatScheme('https');
        }
        Model::unguard();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr']); // also accepts a closure
        });
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                    'Administrator',
                    'SuperAdministrator',
                ]));
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column
                ->toggleable()
                ->searchable();
        });
    }
}
