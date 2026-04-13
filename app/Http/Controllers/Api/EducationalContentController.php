<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationalContent;
use Illuminate\Http\Request;

class EducationalContentController extends Controller
{
    public function index(Request $request)
    {
        $query = EducationalContent::where('is_published', true)
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('tags', 'like', '%' . $request->search . '%'))
            ->latest();

        return response()->json($query->paginate(20));
    }

    public function show(EducationalContent $educationalContent)
    {
        $educationalContent->increment('views_count');

        return response()->json($educationalContent->load('author:id,name,avatar'));
    }

    public function store(Request $request)
    {
        if (! $request->user()->isAdmin() && ! $request->user()->isDoctor()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'type'      => 'required|in:article,video,infographic,podcast',
            'category'  => 'required|in:vaccination,prevention,nutrition,hygiene,first_aid,chronic_diseases',
            'media_url' => 'nullable|url',
            'tags'      => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('education', 'public');
        }

        $content = EducationalContent::create($data);

        return response()->json(['message' => 'Contenu créé.', 'content' => $content], 201);
    }

    public function update(Request $request, EducationalContent $educationalContent)
    {
        if (! $request->user()->isAdmin() && $educationalContent->author_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'content'      => 'sometimes|string',
            'is_published' => 'boolean',
            'tags'         => 'nullable|string',
        ]);

        $educationalContent->update($data);

        return response()->json(['message' => 'Contenu mis à jour.', 'content' => $educationalContent]);
    }
}
