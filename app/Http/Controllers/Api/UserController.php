<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $query = User::with('zone:id,name')
            ->when($request->filled('role'), fn($q) => $q->where('role', $request->role))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'))
            ->latest();

        return response()->json($query->paginate(20));
    }

    public function show(Request $request, User $user)
    {
        if (! $request->user()->isAdmin() && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($user->load(['doctorProfile', 'zone']));
    }

    public function toggleActive(Request $request, User $user)
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return response()->json([
            'message' => $user->is_active ? 'Compte activé.' : 'Compte suspendu.',
            'user'    => $user,
        ]);
    }
}
