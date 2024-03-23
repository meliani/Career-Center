<?php

namespace App\Providers\Filament;

use Amendozaaguiar\FilamentRouteStatistics\FilamentRouteStatisticsPlugin;
use Awcodes\LightSwitch\Enums\Alignment;
use Awcodes\LightSwitch\LightSwitchPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Hydrat\TableLayoutToggle\TableLayoutTogglePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use JibayMcs\FilamentTour\FilamentTourPlugin;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Boredom\Enums\Variants;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            // ->topbar(false)
            // ->topNavigation()
            // ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarCollapsibleOnDesktop()
            ->passwordReset()
            ->profile(isSimple: true)
            ->spa()
            ->maxContentWidth(MaxWidth::Full)
            ->default()
            ->id('Administration')
            ->path('backend')
            // ->brandName('INPT Entreprises')
            ->login()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandLogo(asset('/svg/logo_entreprises.svg'))
            ->favicon(asset('/svg/logo_entreprises_round.svg'))
            ->darkModeBrandLogo(asset('/svg/logo_entreprises_white.svg'))
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Administration/Resources'), for: 'App\\Filament\\Administration\\Resources')
            ->discoverPages(in: app_path('Filament/Administration/Pages'), for: 'App\\Filament\\Administration\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->resources([
                config('filament-logger.activity_resource'),
            ])

            ->plugins(
                [
                    // FilamentRouteStatisticsPlugin::make(),
                    FilamentFullCalendarPlugin::make(),
                    // ->selectable()
                    // ->editable(),
                    FilamentApexChartsPlugin::make(),
                    new \RickDBCN\FilamentEmail\FilamentEmail(),
                    // \Rabol\FilamentLogviewer\FilamentLogviewerPlugin::make(),
                    TableLayoutTogglePlugin::make()
                        ->setDefaultLayout('grid') // default layout to be displayed
                        ->persistLayoutInLocalStorage(true) // allow user to keep his layout preference in his local storage
                        ->shareLayoutBetweenPages(true) // allow all tables to share the layout option (requires persistLayoutInLocalStorage to be true)
                        ->displayToggleAction() // used to display the toogle button automatically, on the desired filament hook (defaults to table bar)
                        ->listLayoutButtonIcon('heroicon-o-list-bullet')
                        ->gridLayoutButtonIcon('heroicon-o-squares-2x2'),
                    FilamentJobsMonitorPlugin::make(),
                    LightSwitchPlugin::make()->position(Alignment::BottomCenter),
                    EnvironmentIndicatorPlugin::make()
                        ->visible(fn () => auth()->user()?->isAdministrator()),
                    // \LaraZeus\Boredom\BoringAvatarPlugin::make()
                    //     ->variant(Variants::BEAM)
                    //     ->size(60)
                    //     ->square()
                    //     ->colors(['0A0310', '49007E', 'FF005B', 'FF7D10', 'FFB238']),
                    SpatieLaravelTranslatablePlugin::make()->defaultLocales([config('app.locale')]),
                    // BoltPlugin::make(),
                    // FilamentTourPlugin::make()
                    //     ->onlyVisibleOnce(true),
                ]
            )
            // ->defaultAvatarProvider(
            //     // \LaraZeus\Boredom\BoringAvatarsProvider::class
            // )
            ->navigationGroups([
                'Students and projects',
                'Étudiants et projets',
                'Juries',
                'Plannings',
                'Entreprises',
                'Planification',
                'Emails',
                'Settings',
                'Paramètres',
                'System',
                'Système',
            ]);
    }
}
