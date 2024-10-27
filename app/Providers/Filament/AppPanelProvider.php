<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\Login;
use App\Filament\App\Pages\RegisterStudent;
use App\Filament\App\Pages\WelcomeDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
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
            ->viteTheme('resources/css/filament/app/theme.css')
            // ->topbar(false)
            ->topNavigation()
            ->authGuard('students')
            ->authPasswordBroker('students')
            ->id('app')
            ->path('app')
            ->colors(
                \App\Services\ColorService::studentsAppColorsOfTheDay()
            )
            ->passwordReset()
            ->emailVerification()
            ->profile(isSimple: false) //isSimple: true)
            ->spa()
            // ->default()
            // ->brandName(__('Engineer portal'))
            ->login()
            // ->login(LoginStudent::class)
            // ->registration(RegisterStudent::class)
            ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            ->brandLogo(asset('/svg/logo_entreprises_vectorized.svg'))
            ->favicon(asset('/svg/logo_entreprises_round.svg'))
            ->darkModeBrandLogo(asset('/svg/logo_entreprises_white_vectorized.svg'))

            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                WelcomeDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
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
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin::make(),

                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: true, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: __('Settings'), // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    // ->customMyProfilePage(\App\Livewire\StudentAccountSettingsPage::class)
                    ->withoutMyProfileComponents([
                        // 'personal_info',
                    ])
                    ->myProfileComponents([\App\Livewire\StudentAccountSettingsPage::class])
                    ->enableTwoFactorAuthentication()
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel()),
                // ->enableSanctumTokens(
                //     permissions: ['view'] // optional, customize the permissions (default = ["create", "view", "update", "delete"])
                // ),
            ])
            ->login(Login::class)
            ->navigationGroups([
                'Internship Offers',
                'Offres de Stages',
                'Final Project',
                'PFE',
                'Settings',
            ])
            ->breadcrumbs(false);
    }
}
