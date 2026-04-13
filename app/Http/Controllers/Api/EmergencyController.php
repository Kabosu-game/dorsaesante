<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyAlert;
use App\Models\HealthStructure;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    public function sendAlert(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|in:medical,accident,fire,other',
            'description' => 'nullable|string|max:500',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'address'     => 'nullable|string',
        ]);

        $data['user_id'] = $request->user()->id;

        // Trouver la structure d'urgence la plus proche
        $nearest = $this->findNearestEmergencyStructure($data['latitude'], $data['longitude']);
        if ($nearest) {
            $data['nearest_structure_id'] = $nearest->id;
        }

        $alert = EmergencyAlert::create($data);

        return response()->json([
            'message'           => 'Alerte d\'urgence envoyée.',
            'alert'             => $alert,
            'nearest_structure' => $nearest,
        ], 201);
    }

    public function nearbyStructures(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'numeric|min:1|max:100',
        ]);

        $lat  = $request->latitude;
        $lng  = $request->longitude;
        $radius = $request->input('radius_km', 10);

        // Formule Haversine pour calculer la distance
        $structures = HealthStructure::selectRaw("
                *,
                (6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(latitude))
                )) AS distance_km
            ", [$lat, $lng, $lat])
            ->where('is_active', true)
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km')
            ->with('services')
            ->get();

        return response()->json($structures);
    }

    public function myAlerts(Request $request)
    {
        $alerts = EmergencyAlert::where('user_id', $request->user()->id)
            ->with('nearestStructure:id,name,address,phone')
            ->latest()
            ->paginate(10);

        return response()->json($alerts);
    }

    public function updateStatus(Request $request, EmergencyAlert $alert)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $request->validate([
            'status'          => 'required|in:received,dispatched,resolved',
            'responder_notes' => 'nullable|string',
        ]);

        $data = $request->only('status', 'responder_notes');
        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $alert->update($data);

        return response()->json(['message' => 'Statut mis à jour.', 'alert' => $alert]);
    }

    private function findNearestEmergencyStructure(float $lat, float $lng): ?HealthStructure
    {
        return HealthStructure::selectRaw("
                *,
                (6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(latitude))
                )) AS distance_km
            ", [$lat, $lng, $lat])
            ->where('is_active', true)
            ->where('has_emergency', true)
            ->having('distance_km', '<=', 50)
            ->orderBy('distance_km')
            ->first();
    }
}
