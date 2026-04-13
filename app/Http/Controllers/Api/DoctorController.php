<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\CachesPublicIndex;
use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use CachesPublicIndex;

    public function index(Request $request)
    {
        $query = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with(['doctorProfile', 'zone:id,name'])
            ->when($request->filled('specialty'), fn($q) => $q->whereHas('doctorProfile', fn($p) => $p->where('specialty', 'like', '%' . $request->specialty . '%')))
            ->when($request->filled('zone_id'), fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('teleconsult'), fn($q) => $q->whereHas('doctorProfile', fn($p) => $p->where('available_teleconsult', true)));

        return $this->cachedPaginate($request, 'dorsa.api.doctors', $query);
    }

    public function show(User $doctor)
    {
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'Médecin introuvable.'], 404);
        }

        return response()->json($doctor->load(['doctorProfile.healthStructure', 'zone']));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (! $user->isDoctor()) {
            return response()->json(['message' => 'Réservé aux médecins.'], 403);
        }

        $data = $request->validate([
            'specialty'             => 'sometimes|string|max:100',
            'license_number'        => 'sometimes|string|unique:doctor_profiles,license_number,' . optional($user->doctorProfile)->id,
            'diploma'               => 'nullable|string',
            'bio'                   => 'nullable|string|max:1000',
            'structure_name'        => 'nullable|string',
            'health_structure_id'   => 'nullable|exists:health_structures,id',
            'consultation_fee'      => 'nullable|numeric|min:0',
            'available_teleconsult' => 'boolean',
            'experience_years'      => 'integer|min:0',
            'languages'             => 'nullable|string',
        ]);

        $profile = $user->doctorProfile()->updateOrCreate(['user_id' => $user->id], $data);

        return response()->json(['message' => 'Profil médecin mis à jour.', 'profile' => $profile]);
    }

    public function availability(Request $request, User $doctor)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $booked = $doctor->appointmentsAsDoctor()
            ->whereDate('scheduled_at', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('scheduled_at')
            ->map(fn($dt) => $dt->format('H:i'));

        // Créneaux disponibles de 08h à 18h par tranche de 30min
        $slots = [];
        for ($h = 8; $h < 18; $h++) {
            foreach (['00', '30'] as $m) {
                $time = sprintf('%02d:%s', $h, $m);
                $slots[] = ['time' => $time, 'available' => ! $booked->contains($time)];
            }
        }

        return response()->json(['date' => $request->date, 'slots' => $slots]);
    }
}
