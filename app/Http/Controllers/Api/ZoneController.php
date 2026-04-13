<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        return response()->json(Zone::with('children')->whereNull('parent_id')->get());
    }

    public function store(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|unique:zones',
            'type'      => 'required|in:region,district,commune,quartier',
            'parent_id' => 'nullable|exists:zones,id',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $zone = Zone::create($data);

        return response()->json(['message' => 'Zone créée.', 'zone' => $zone], 201);
    }
}
