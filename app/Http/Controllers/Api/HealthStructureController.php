<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\CachesPublicIndex;
use App\Http\Controllers\Controller;
use App\Models\HealthStructure;
use Illuminate\Http\Request;

class HealthStructureController extends Controller
{
    use CachesPublicIndex;

    public function index(Request $request)
    {
        $query = HealthStructure::with(['zone:id,name', 'services'])
            ->where('is_active', true)
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('zone_id'), fn($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('has_emergency'), fn($q) => $q->where('has_emergency', true))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));

        return $this->cachedPaginate($request, 'dorsa.api.health_structures', $query);
    }

    public function show(HealthStructure $healthStructure)
    {
        return response()->json($healthStructure->load(['zone', 'services', 'doctors.user:id,name,avatar']));
    }

    public function store(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:hopital,clinique,centre_sante,pharmacie,urgence',
            'description'   => 'nullable|string',
            'address'       => 'required|string',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email',
            'website'       => 'nullable|url',
            'zone_id'       => 'nullable|exists:zones,id',
            'has_emergency' => 'boolean',
            'has_teleconsult' => 'boolean',
            'opening_hours' => 'nullable|string',
            'image'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('structures', 'public');
        }

        $structure = HealthStructure::create($data);

        return response()->json(['message' => 'Structure créée.', 'structure' => $structure], 201);
    }

    public function update(Request $request, HealthStructure $healthStructure)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'type'           => 'sometimes|in:hopital,clinique,centre_sante,pharmacie,urgence',
            'description'    => 'nullable|string',
            'address'        => 'sometimes|string',
            'latitude'       => 'sometimes|numeric',
            'longitude'      => 'sometimes|numeric',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'zone_id'        => 'nullable|exists:zones,id',
            'has_emergency'  => 'boolean',
            'has_teleconsult'=> 'boolean',
            'opening_hours'  => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        $healthStructure->update($data);

        return response()->json(['message' => 'Structure mise à jour.', 'structure' => $healthStructure]);
    }

    public function destroy(Request $request, HealthStructure $healthStructure)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $healthStructure->update(['is_active' => false]);

        return response()->json(['message' => 'Structure désactivée.']);
    }
}
