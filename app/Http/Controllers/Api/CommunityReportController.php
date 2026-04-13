<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityReport;
use Illuminate\Http\Request;

class CommunityReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = CommunityReport::with(['user:id,name', 'zone:id,name'])
            ->when($user->isPatient() || $user->isDoctor(), fn($q) => $q->where('user_id', $user->id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->latest();

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'         => 'required|in:suspected_disease,hygiene_issue,health_concern,pollution',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'address'      => 'nullable|string',
            'zone_id'      => 'nullable|exists:zones,id',
            'is_anonymous' => 'boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $img) {
                $paths[] = $img->store('reports', 'public');
            }
            $data['images'] = $paths;
        }

        $report = CommunityReport::create($data);

        return response()->json(['message' => 'Signalement soumis. Merci pour votre vigilance.', 'report' => $report], 201);
    }

    public function show(CommunityReport $communityReport)
    {
        $user = request()->user();
        if (! $user->isAdmin() && $communityReport->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($communityReport->load(['user:id,name', 'zone:id,name']));
    }

    public function respond(Request $request, CommunityReport $communityReport)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Réservé aux administrateurs.'], 403);
        }

        $data = $request->validate([
            'status'         => 'required|in:under_review,resolved,dismissed',
            'admin_response' => 'nullable|string',
        ]);

        $communityReport->update($data);

        return response()->json(['message' => 'Réponse enregistrée.', 'report' => $communityReport]);
    }
}
