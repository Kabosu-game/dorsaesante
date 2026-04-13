<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AppointmentsChart;
use App\Filament\Widgets\EmergencyAlertsTable;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Dorsa e-Santé')
            ->brandLogo(null)
            ->colors([
                'primary' => Color::Teal,
                'danger'  => Color::Red,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info'    => Color::Blue,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label('Utilisateurs'),
                NavigationGroup::make()->label('Soins & Consultations'),
                NavigationGroup::make()->label('Contenu & Éducation'),
                NavigationGroup::make()->label('Alertes & Urgences'),
                NavigationGroup::make()->label('Géographie'),
                NavigationGroup::make()->label('Statistiques'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                AppointmentsChart::class,
                EmergencyAlertsTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->authGuard('web');
    }
}
