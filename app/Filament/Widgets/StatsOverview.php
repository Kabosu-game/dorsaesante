<?php

namespace App\Filament\Widgets;

use App\Services\AdminStatsAggregator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $s = AdminStatsAggregator::filamentOverviewRow();

        return [
            Stat::make('Utilisateurs', $s['users_total'])
                ->description($s['users_new_month'] . ' ce mois')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 4, 6, 8, 10, 12, 15]),

            Stat::make('Patients', $s['patients_total'])
                ->description($s['patients_active'] . ' actifs')
                ->color('info'),

            Stat::make('Médecins', $s['doctors_total'])
                ->description($s['doctors_active'] . ' actifs')
                ->color('primary'),

            Stat::make('Rendez-vous', $s['appointments_total'])
                ->description($s['appointments_pending'] . ' en attente')
                ->color('warning'),

            Stat::make('Urgences', $s['emergencies_total'])
                ->description($s['emergencies_sent'] . ' non traitées')
                ->color('danger'),

            Stat::make('RDV ce mois', $s['appointments_month'])
                ->description('Rendez-vous du mois en cours')
                ->color('success'),
        ];
    }
}
