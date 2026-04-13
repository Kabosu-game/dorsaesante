<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;

class AppointmentsChart extends ChartWidget
{
    protected ?string $heading = 'Rendez-vous des 7 derniers jours';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $rows = Appointment::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as c')
            ->groupByRaw('DATE(created_at)')
            ->pluck('c', 'day');

        $data = collect(range(6, 0))->map(function ($daysAgo) use ($rows) {
            $d = now()->subDays($daysAgo)->format('Y-m-d');

            return (int) ($rows[$d] ?? 0);
        });

        $labels = collect(range(6, 0))->map(fn($d) => now()->subDays($d)->format('d/m'));

        return [
            'datasets' => [
                [
                    'label' => 'Rendez-vous',
                    'data' => $data->toArray(),
                    'borderColor' => '#0d9488',
                    'backgroundColor' => 'rgba(13, 148, 136, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
