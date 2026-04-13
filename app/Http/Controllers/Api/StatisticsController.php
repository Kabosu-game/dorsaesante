<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Services\AdminStatsAggregator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function dashboard(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Accès réservé aux administrateurs.'], 403);
        }

        return response()->json(AdminStatsAggregator::dashboardApiPayload());
    }

    public function appointmentsByZone(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = Appointment::join('users', 'users.id', '=', 'appointments.patient_id')
            ->join('zones', 'zones.id', '=', 'users.zone_id')
            ->select('zones.name as zone', DB::raw('COUNT(*) as total'))
            ->groupBy('zones.name')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function topDiseases(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = MedicalRecord::whereNotNull('diagnosis')
            ->select('diagnosis', DB::raw('COUNT(*) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    public function myStats(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $m = (int) $now->month;
        $y = (int) $now->year;

        if ($user->isDoctor()) {
            $uid = $user->id;
            $apts = DB::table('appointments')
                ->where('doctor_id', $uid)
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
                ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
                ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as this_month', [$m, $y])
                ->first();

            $tele = DB::table('teleconsultations')->where('doctor_id', $uid)->count();
            $lives = DB::table('live_streams')->where('doctor_id', $uid)->count();
            $patientsServed = (int) DB::table('appointments')
                ->where('doctor_id', $uid)
                ->selectRaw('COUNT(DISTINCT patient_id) as c')
                ->value('c');

            return response()->json([
                'appointments' => [
                    'total' => (int) ($apts->total ?? 0),
                    'pending' => (int) ($apts->pending ?? 0),
                    'completed' => (int) ($apts->completed ?? 0),
                    'this_month' => (int) ($apts->this_month ?? 0),
                ],
                'teleconsultations' => $tele,
                'live_streams' => $lives,
                'patients_served' => $patientsServed,
            ]);
        }

        $uid = $user->id;
        $apts = DB::table('appointments')
            ->where('patient_id', $uid)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'confirmed' AND scheduled_at > ? THEN 1 ELSE 0 END) as upcoming", [$now])
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->first();

        $records = DB::table('medical_records')->where('patient_id', $uid)->count();
        $reports = DB::table('community_reports')->where('user_id', $uid)->count();

        return response()->json([
            'appointments' => [
                'total' => (int) ($apts->total ?? 0),
                'upcoming' => (int) ($apts->upcoming ?? 0),
                'completed' => (int) ($apts->completed ?? 0),
            ],
            'medical_records' => $records,
            'reports' => $reports,
        ]);
    }
}
