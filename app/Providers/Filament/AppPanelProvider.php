<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\RegisterStudent;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->topbar(false)
            ->topNavigation()
            ->authGuard('students')
            ->authPasswordBroker('students')
            ->id('app')
            ->path('app')
            ->colors([
                'primary' => Color::Sky,
            ])

            ->passwordReset()
            ->emailVerification()
            ->profile(isSimple: false) //isSimple: true)
            ->spa()
            // ->default()
            // ->brandName(__('Engineer portal'))
            ->login()
            ->registration(RegisterStudent::class)
            ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            ->brandLogo(asset('/svg/logo_entreprises.svg'))
            ->favicon(asset('/svg/logo_entreprises_round.svg'))
            ->darkModeBrandLogo(asset('/svg/logo_entreprises_white.svg'))

            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
