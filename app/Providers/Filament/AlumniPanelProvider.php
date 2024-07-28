<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AlumniPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('alumni')
            ->path('alumni')
            ->passwordReset()
            ->emailVerification()
            ->breadcrumbs(false)
            ->profile(isSimple: false)
            ->registration(\App\Filament\Alumni\Pages\RegisterAlumni::class)
            // ->topbar(false)
            ->topNavigation()
            ->sidebarFullyCollapsibleOnDesktop()
            // ->databaseTransactions()
            ->authGuard('alumnis')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            // ->maxContentWidth(MaxWidth::Full)
            ->brandName(__('Career Center'))
            ->login()
            ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            ->brandLogo(asset('/svg/logo_alumni.svg'))
            ->favicon(asset('/svg/logo_entreprises_round.svg'))
            ->darkModeBrandLogo(asset('/svg/logo_alumni_white.svg'))
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Sky,
                'info' => Color::Sky,
                'primary' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Yellow,
                'secondary' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Alumni/Resources'), for: 'App\\Filament\\Alumni\\Resources')
            ->discoverPages(in: app_path('Filament/Alumni/Pages'), for: 'App\\Filament\\Alumni\\Pages')
            ->pages([
                \App\Filament\Alumni\Pages\WelcomeDashboard::class,
                \App\Filament\Alumni\Pages\MyProfilePage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Alumni/Widgets'), for: 'App\\Filament\\Alumni\\Widgets')
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin::make(),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        // shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        // shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        // navigationGroup: __('Settings'), // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->customMyProfilePage(\App\Filament\Alumni\Pages\MyProfilePage::class)
                    // ->withoutMyProfileComponents([
                    //     'personal_info',
                    // ])
                    ->myProfileComponents(['alumni_info' => \App\Livewire\AlumniAccountSettingsPage::class,
                        'personal_info' => \App\Livewire\AlumniPersonalInfo::class,
                    ])
                    ->enableTwoFactorAuthentication()
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel()->disk('alumni-profile-photos')),
            ]);
    }
}
