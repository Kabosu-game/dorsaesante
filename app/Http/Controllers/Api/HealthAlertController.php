<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\CachesPublicIndex;
use App\Http\Controllers\Controller;
use App\Models\HealthAlert;
use Illuminate\Http\Request;

class HealthAlertController extends Controller
{
    use CachesPublicIndex;

    public function index(Request $request)
    {
        $query = HealthAlert::with(['zone:id,name', 'author:id,name'])
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->when($request->filled('level'), fn($q) => $q->where('level', $request->level))
            ->when($request->filled('zone_id'), fn($q) => $q->where(fn($q) => $q->where('zone_id', $request->zone_id)->orWhereNull('zone_id')))
            ->latest();

        return $this->cachedPaginate($request, 'dorsa.api.health_alerts', $query);
    }

    public function show(HealthAlert $healthAlert)
    {
        return response()->json($healthAlert->load(['zone', 'author:id,name']));
    }

    public function store(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Réservé aux administrateurs.'], 403);
        }

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'message'      => 'required|string',
            'level'        => 'required|in:info,warning,danger,critical',
            'type'         => 'required|in:epidemic,campaign,environmental,emergency,vaccination',
            'zone_id'      => 'nullable|exists:zones,id',
            'expires_at'   => 'nullable|date|after:now',
            'target_roles' => 'nullable|array',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('alerts', 'public');
        }

        $alert = HealthAlert::create($data);

        return response()->json(['message' => 'Alerte créée et diffusée.', 'alert' => $alert], 201);
    }

    public function deactivate(Request $request, HealthAlert $healthAlert)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $healthAlert->update(['is_active' => false]);

        return response()->json(['message' => 'Alerte désactivée.']);
    }
}
