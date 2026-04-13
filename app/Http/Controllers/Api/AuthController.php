<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'in:patient,doctor',
            'birth_date' => 'nullable|date',
            'gender'     => 'nullable|in:male,female,other',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'patient';

        $user = User::create($data);
        $token = $user->createToken('dorsaesante')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Compte désactivé. Contactez l\'administration.'], 403);
        }

        $token = $user->createToken('dorsaesante')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $user->load('doctorProfile', 'zone'),
            'token'   => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('doctorProfile', 'zone'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'phone'      => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'birth_date' => 'nullable|date',
            'gender'     => 'nullable|in:male,female,other',
            'address'    => 'nullable|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'fcm_token'  => 'nullable|string',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json(['message' => 'Profil mis à jour.', 'user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect.'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Mot de passe modifié avec succès.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }
}
