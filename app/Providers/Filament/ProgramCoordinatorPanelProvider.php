<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Hydrat\TableLayoutToggle\TableLayoutTogglePlugin;

class ProgramCoordinatorPanelProvider extends PanelProvider
{
    // public static ?string $label = 'Program Coordinator Panel';
    // protected static string $routePath = 'backend/admin';
    // // protected static ?string $title = 'Program coordinator dashboard';
    public function panel(Panel $panel): Panel
    {
        return $panel
        ->passwordReset()
        ->profile()
        ->spa()
        ->maxContentWidth(MaxWidth::Full)
        ->default()
        ->brandName('INPT Entreprises')
        ->login()
        ->colors([
            'primary' => Color::Amber,
        ])
            ->id('programCoordinator')
            ->path('programCoordinator')

            ->discoverResources(in: app_path('Filament/ProgramCoordinator/Resources'), for: 'App\\Filament\\ProgramCoordinator\\Resources')
            ->discoverPages(in: app_path('Filament/ProgramCoordinator/Pages'), for: 'App\\Filament\\ProgramCoordinator\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/ProgramCoordinator/Widgets'), for: 'App\\Filament\\ProgramCoordinator\\Widgets')
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
            ->plugins([
                FilamentFullCalendarPlugin::make(),
                // ->selectable()
                // ->editable(),
                FilamentApexChartsPlugin::make(),

                TableLayoutTogglePlugin::make()
                    ->persistLayoutInLocalStorage(true) // allow user to keep his layout preference in his local storage
                    ->shareLayoutBetweenPages(true) // allow all tables to share the layout option (requires persistLayoutInLocalStorage to be true)
                    ->displayToggleAction() // used to display the toogle button automatically, on the desired filament hook (defaults to table bar)
                    ->listLayoutButtonIcon('heroicon-o-list-bullet')
                    ->gridLayoutButtonIcon('heroicon-o-squares-2x2'),
            ]);
    }
}
