<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentalHealthResource;
use Illuminate\Http\Request;

class MentalHealthController extends Controller
{
    public function index(Request $request)
    {
        $query = MentalHealthResource::where('is_published', true)
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->latest();

        return response()->json($query->paginate(20));
    }

    public function show(MentalHealthResource $mentalHealthResource)
    {
        return response()->json($mentalHealthResource->load('author:id,name,avatar'));
    }

    public function store(Request $request)
    {
        if (! $request->user()->isDoctor() && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'title'                => 'required|string|max:255',
            'content'              => 'required|string',
            'type'                 => 'required|in:article,video,audio,quiz,exercise',
            'category'             => 'required|in:stress,depression,anxiety,sleep,addiction',
            'media_url'            => 'nullable|url',
            'requires_professional'=> 'boolean',
            'duration_minutes'     => 'nullable|integer',
            'thumbnail'            => 'nullable|image|max:2048',
        ]);

        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('mental-health', 'public');
        }

        $resource = MentalHealthResource::create($data);

        return response()->json(['message' => 'Ressource créée.', 'resource' => $resource], 201);
    }
}
