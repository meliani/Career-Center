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
use Hugomyb\FilamentErrorMailer\FilamentErrorMailerPlugin;
use Hydrat\TableLayoutToggle\TableLayoutTogglePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use JibayMcs\FilamentTour\FilamentTourPlugin;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Boredom\Enums\Variants;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            // ->topbar(false)
            // ->topNavigation()
            // ->sidebarFullyCollapsibleOnDesktop()
            // ->databaseTransactions()
            ->sidebarCollapsibleOnDesktop()
            ->passwordReset()
            ->profile() //isSimple: true)
            ->spa()
            ->maxContentWidth(MaxWidth::Full)
            ->default()
            ->id('Administration')
            ->path('backend')
            ->brandName(__('Career Center'))
            ->login()
            ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            ->brandLogo(asset('/svg/logo_entreprises.svg'))
            ->favicon(asset('/svg/logo_entreprises_round.svg'))
            ->darkModeBrandLogo(asset('/svg/logo_entreprises_white.svg'))
            ->colors(
                \App\Services\ColorService::colorsOfTheDay()
                //     [
                //     'danger' => Color::Rose,
                //     'gray' => Color::Gray,
                //     'info' => Color::Blue,
                //     'primary' => Color::Indigo,
                //     'success' => Color::Emerald,
                //     'warning' => Color::Orange,
                //     'secondary' => Color::Cyan,
                // ]
            )
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
                // 'auth:sanctum',
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])

            ->plugins(
                [
                    SimpleLightBoxPlugin::make(),
                    // FilamentRouteStatisticsPlugin::make(),
                    FilamentFullCalendarPlugin::make()
                        // ->schedulerLicenseKey('GPL-My-Project-Is-Open-Source')
                        ->schedulerLicenseKey('CC-Attribution-NonCommercial-NoDerivatives')
                        ->selectable(false)
                        ->editable(false)
                        ->timezone(env('APP_TIMEZONE', 'UTC'))
                        ->plugins([
                            // these plugins are already included ['dayGrid', 'timeGrid', 'interaction', 'list', 'moment', 'momentTimezone']
                            // 'scrollGrid',
                            'timeline',
                            'resourceTimeline',
                            'resourceTimeGrid',
                        ])
                        ->config([
                            'initialView' => 'timeGridDay', //timeGridWeek, timeGridDay, dayGridMonth, listWeek
                            'firstDay' => 1, // start the week on a Monday
                            'eventDisplay' => 'list-item', // block, list-item, auto, background, inverse-background and none
                            'eventTimeFormat' => [
                                'hour' => 'numeric',
                                'minute' => '2-digit',
                                'hour12' => false,
                                'meridiem' => 'false',
                            ],
                            'displayEventEnd' => true,
                            'slotDuration' => '00:15:00',
                            'slotMinTime' => '08:00:00',
                            'slotMaxTime' => '16:30:00',
                            'allDaySlot' => false,
                            'slotLabelFormat' => [
                                'hour' => 'numeric',
                                'minute' => '2-digit',
                                'hour12' => false,
                                'meridiem' => 'false',
                            ],
                            'slotLabelInterval' => '00:90:00',
                            // 'slotLabelContent' => 'hour',
                            'expandRows' => true,
                            'slotEventOverlap' => false,
                            'nowIndicator' => true,
                            // 'views' => [
                            //     'timeGridWeek' => [
                            //         'titleFormat' => ['year', 'month', 'day'],
                            //     ],
                            //     'timeGridDay' => [
                            //         'titleFormat' => ['year', 'month', 'day'],
                            //     ],
                            //     'dayGridMonth' => [
                            //         'titleFormat' => ['year', 'month'],
                            //     ],
                            //     'listWeek' => [
                            //         'titleFormat' => ['year', 'month', 'day'],
                            //     ],
                            // ],
                            'resources' => [
                                [
                                    'id' => '1',
                                    'title' => 'Amphi 1',
                                ],
                                [
                                    'id' => '2',
                                    'title' => 'Amphi 2',
                                ],
                                [
                                    'id' => '3',
                                    'title' => 'Amphi 3',
                                ],
                                // [
                                //     'id' => '5',
                                //     'title' => 'Salle B10',
                                // ],
                                [
                                    'id' => '6',
                                    'title' => 'Salle B12',
                                ],

                                [
                                    'id' => '7',
                                    'title' => 'Salle B202',
                                ],
                                // [
                                //     'id' => '8',
                                //     'title' => 'Salle B119',
                                // ],
                                [
                                    'id' => '9',
                                    'title' => 'Salle CF',
                                ],
                            ],
                            'views' => [
                                'timeGridWeek' => [
                                    'type' => 'timeGrid',
                                    // 'duration' => ['days' => 2],
                                    'hiddenDays' => [0, 6],
                                    'buttonText' => __('Time Grid Week'),
                                ],
                                'timeGridDay' => [
                                    'type' => 'timeGridDay',
                                    // 'duration' => ['days' => 1],
                                    'hiddenDays' => [0, 6],
                                    'buttonText' => __('Time Grid Day'),
                                ],
                                'resourceTimeGridDay' => [
                                    'type' => 'resourceTimeGrid',
                                    'duration' => ['days' => 1],
                                    'hiddenDays' => [0, 6],
                                    'text' => 'resourceTimeGridDay',
                                    'buttonText' => __('Resource Time Grid Day'),
                                ],
                            ],
                            // 'buttonText' => [
                            //     'today' => 'Aujourd\'hui',
                            //     'month' => 'Mois',
                            //     'week' => 'Semainegfhf',
                            //     'day' => 'Jourhfg',
                            //     'list' => 'Listehgfhgf',
                            // ],
                            'headerToolbar' => [
                                'start' => 'prev,next',
                                'center' => 'title',
                                'end' => 'today timeGridWeek,timeGridDay resourceTimeGridDay',
                                // 'end' => 'today timeGridWeek,timeGridDay timelineWeek,timelineDay resourceTimelineWeek,resourceTimelineDay',
                            ],
                            'eventContent' => 'function(arg) {
                                return { html: "<b>" + arg.event.title + "</b><br/>" + arg.event.extendedProps.description };
                            }',
                            'initialDate' => now(),

                        ])
                        ->locale('fr'),
                    FilamentApexChartsPlugin::make(),
                    // new \RickDBCN\FilamentEmail\FilamentEmail(),
                    // \Rabol\FilamentLogviewer\FilamentLogviewerPlugin::make(),
                    TableLayoutTogglePlugin::make()
                        ->setDefaultLayout('grid') // default layout to be displayed
                        ->persistLayoutInLocalStorage(true) // allow user to keep his layout preference in his local storage
                        ->shareLayoutBetweenPages(true) // allow all tables to share the layout option (requires persistLayoutInLocalStorage to be true)
                        ->displayToggleAction() // used to display the toogle button automatically, on the desired filament hook (defaults to table bar)
                        ->listLayoutButtonIcon('heroicon-o-list-bullet')
                        ->gridLayoutButtonIcon('heroicon-o-squares-2x2'),
                    // FilamentJobsMonitorPlugin::make(),
                    LightSwitchPlugin::make()->position(Alignment::BottomCenter),
                    EnvironmentIndicatorPlugin::make()
                        ->visible(fn () => auth()->user()?->isSuperAdministrator()),
                    // \LaraZeus\Boredom\BoringAvatarPlugin::make()
                    //     ->variant(Variants::BEAM)
                    //     ->size(60)
                    //     ->square()
                    //     ->colors(['0A0310', '49007E', 'FF005B', 'FF7D10', 'FFB238']),
                    SpatieLaravelTranslatablePlugin::make()->defaultLocales([config('app.locale')]),
                    // BoltPlugin::make(),
                    // FilamentTourPlugin::make()
                    //     ->onlyVisibleOnce(true),
                    SpotlightPlugin::make(),
                    // FilamentErrorMailerPlugin::make(),
                    FilamentPeekPlugin::make(),
                    BreezyCore::make()
                        ->myProfile(
                            shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                            shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                            navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                            hasAvatars: true, // Enables the avatar upload form component (default = false)
                            slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                        )
                        ->enableTwoFactorAuthentication()
                        ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel())
                        ->enableSanctumTokens(
                            permissions: ['view'] // optional, customize the permissions (default = ["create", "view", "update", "delete"])
                        ),

                ]
            )
            // ->defaultAvatarProvider(
            //     // \LaraZeus\Boredom\BoringAvatarsProvider::class
            // )
            ->navigationGroups([
                'Students and projects',
                'Étudiants et projets',
                'Juries',
                'Calendars',
                'Calendriers',
                'Plannings',
                'Listes des planings',
                'Entreprises',
                'Planification',
                'Support',
                'Emails',
                'Settings',
                'Paramètres',
                'System',
                'Système',
            ]);
    }
}
