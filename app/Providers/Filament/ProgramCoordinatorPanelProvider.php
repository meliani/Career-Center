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
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class ProgramCoordinatorPanelProvider extends PanelProvider
{
    // public static ?string $label = 'Program Coordinator Panel';
    // protected static string $routePath = 'backend/admin';
    // // protected static ?string $title = 'Program coordinator dashboard';
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('programCoordinator')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->spa()
            ->maxContentWidth(MaxWidth::Full)
            ->brandName('INPT Entreprises')
            ->login()
            ->path('programCoordinator')
            ->colors([
                'primary' => Color::Amber,
            ])
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
            ]);
    }
}
