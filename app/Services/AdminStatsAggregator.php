<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Agrégations SQL en une passe pour le dashboard admin / API stats (évite 15–20 COUNT séquentiels).
 */
final class AdminStatsAggregator
{
    public static function dashboardApiPayload(): array
    {
        $now = Carbon::now();
        $m = (int) $now->month;
        $y = (int) $now->year;

        $users = DB::table('users')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN role = 'patient' THEN 1 ELSE 0 END) as patients")
            ->selectRaw("SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as doctors")
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as new_this_month', [$m, $y])
            ->first();

        $appointments = DB::table('appointments')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as this_month', [$m, $y])
            ->first();

        $emergencies = DB::table('emergency_alerts')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved")
            ->first();

        $reports = DB::table('community_reports')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review")
            ->first();

        $structures = DB::table('health_structures')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN has_emergency = 1 THEN 1 ELSE 0 END) as with_emergency')
            ->first();

        return [
            'users' => [
                'total' => (int) ($users->total ?? 0),
                'patients' => (int) ($users->patients ?? 0),
                'doctors' => (int) ($users->doctors ?? 0),
                'new_this_month' => (int) ($users->new_this_month ?? 0),
            ],
            'appointments' => [
                'total' => (int) ($appointments->total ?? 0),
                'pending' => (int) ($appointments->pending ?? 0),
                'confirmed' => (int) ($appointments->confirmed ?? 0),
                'completed' => (int) ($appointments->completed ?? 0),
                'this_month' => (int) ($appointments->this_month ?? 0),
            ],
            'emergencies' => [
                'total' => (int) ($emergencies->total ?? 0),
                'pending' => (int) ($emergencies->pending ?? 0),
                'resolved' => (int) ($emergencies->resolved ?? 0),
            ],
            'reports' => [
                'total' => (int) ($reports->total ?? 0),
                'pending' => (int) ($reports->pending ?? 0),
                'under_review' => (int) ($reports->under_review ?? 0),
            ],
            'structures' => [
                'total' => (int) ($structures->total ?? 0),
                'with_emergency' => (int) ($structures->with_emergency ?? 0),
            ],
        ];
    }

    /**
     * Données pour Filament StatsOverview (une requête agrégée utilisateurs + RDV + urgences).
     *
     * @return array{
     *   users_total: int,
     *   users_new_month: int,
     *   patients_total: int,
     *   patients_active: int,
     *   doctors_total: int,
     *   doctors_active: int,
     *   appointments_total: int,
     *   appointments_pending: int,
     *   appointments_month: int,
     *   emergencies_total: int,
     *   emergencies_sent: int,
     * }
     */
    public static function filamentOverviewRow(): array
    {
        $now = Carbon::now();
        $m = (int) $now->month;
        $y = (int) $now->year;

        $users = DB::table('users')
            ->selectRaw('COUNT(*) as users_total')
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as users_new_month', [$m, $y])
            ->selectRaw("SUM(CASE WHEN role = 'patient' THEN 1 ELSE 0 END) as patients_total")
            ->selectRaw("SUM(CASE WHEN role = 'patient' AND is_active = 1 THEN 1 ELSE 0 END) as patients_active")
            ->selectRaw("SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as doctors_total")
            ->selectRaw("SUM(CASE WHEN role = 'doctor' AND is_active = 1 THEN 1 ELSE 0 END) as doctors_active")
            ->first();

        $apts = DB::table('appointments')
            ->selectRaw('COUNT(*) as appointments_total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as appointments_pending")
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as appointments_month', [$m, $y])
            ->first();

        $emg = DB::table('emergency_alerts')
            ->selectRaw('COUNT(*) as emergencies_total')
            ->selectRaw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as emergencies_sent")
            ->first();

        return [
            'users_total' => (int) ($users->users_total ?? 0),
            'users_new_month' => (int) ($users->users_new_month ?? 0),
            'patients_total' => (int) ($users->patients_total ?? 0),
            'patients_active' => (int) ($users->patients_active ?? 0),
            'doctors_total' => (int) ($users->doctors_total ?? 0),
            'doctors_active' => (int) ($users->doctors_active ?? 0),
            'appointments_total' => (int) ($apts->appointments_total ?? 0),
            'appointments_pending' => (int) ($apts->appointments_pending ?? 0),
            'appointments_month' => (int) ($apts->appointments_month ?? 0),
            'emergencies_total' => (int) ($emg->emergencies_total ?? 0),
            'emergencies_sent' => (int) ($emg->emergencies_sent ?? 0),
        ];
    }
}
