<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\CachesPublicIndex;
use App\Http\Controllers\Controller;
use App\Models\LiveStream;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    use CachesPublicIndex;

    public function index(Request $request)
    {
        $query = LiveStream::with('doctor:id,name,avatar')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('topic'), fn($q) => $q->where('topic', 'like', '%' . $request->topic . '%'))
            ->when(! $request->filled('status'), fn($q) => $q->whereIn('status', ['live', 'scheduled']))
            ->orderByRaw("FIELD(status, 'live', 'scheduled', 'ended')")
            ->latest();

        return $this->cachedPaginate($request, 'dorsa.api.live_streams', $query);
    }

    public function show(LiveStream $liveStream)
    {
        return response()->json($liveStream->load('doctor:id,name,avatar'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isDoctor()) {
            return response()->json(['message' => 'Réservé aux médecins.'], 403);
        }

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'topic'        => 'nullable|string|max:100',
            'scheduled_at' => 'nullable|date|after:now',
            'is_recorded'  => 'boolean',
        ]);

        $data['doctor_id'] = $user->id;
        $data['status'] = 'scheduled';

        $stream = LiveStream::create($data);

        return response()->json([
            'message'    => 'Live créé.',
            'stream'     => $stream,
            'stream_key' => $stream->stream_key,
        ], 201);
    }

    public function goLive(Request $request, LiveStream $liveStream)
    {
        if ($liveStream->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $liveStream->update([
            'status'     => 'live',
            'started_at' => now(),
        ]);

        return response()->json(['message' => 'Vous êtes en live !', 'stream' => $liveStream]);
    }

    public function endLive(Request $request, LiveStream $liveStream)
    {
        if ($liveStream->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $liveStream->update([
            'status'   => 'ended',
            'ended_at' => now(),
        ]);

        return response()->json(['message' => 'Live terminé.', 'stream' => $liveStream]);
    }

    public function updateViewers(Request $request, LiveStream $liveStream)
    {
        $count = $request->input('viewers_count', 0);
        $liveStream->update([
            'viewers_count' => $count,
            'max_viewers'   => max($liveStream->max_viewers, $count),
        ]);

        return response()->json(['updated' => true]);
    }
}
