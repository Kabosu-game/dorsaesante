<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientEmergencyProfile;
use App\Models\User;
use Illuminate\Http\Request;

class PatientEmergencyProfileController extends Controller
{
    /**
     * Fiche urgence / premiers secours du patient connecté.
     */
    public function mine(Request $request)
    {
        $user = $request->user();

        if (! $user->isPatient()) {
            return response()->json(['message' => 'Réservé aux patients.'], 403);
        }

        $profile = PatientEmergencyProfile::where('user_id', $user->id)->first();

        return response()->json([
            'profile' => $profile,
        ]);
    }

    /**
     * Création ou mise à jour par le patient.
     */
    public function updateMine(Request $request)
    {
        $user = $request->user();

        if (! $user->isPatient()) {
            return response()->json(['message' => 'Seuls les patients peuvent remplir cette fiche.'], 403);
        }

        $data = $request->validate([
            'blood_group'               => 'nullable|string|max:16',
            'allergies'                 => 'nullable|string|max:5000',
            'chronic_conditions'        => 'nullable|string|max:5000',
            'regular_medications'       => 'nullable|string|max:5000',
            'emergency_contact_name'    => 'nullable|string|max:255',
            'emergency_contact_phone'   => 'nullable|string|max:32',
            'first_aid_notes'           => 'nullable|string|max:5000',
        ]);

        $profile = PatientEmergencyProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Fiche enregistrée.',
            'profile' => $profile->fresh(),
        ]);
    }

    /**
     * Consultation par administrateur ou médecin (dossier patient).
     */
    public function forPatient(Request $request, User $user)
    {
        $auth = $request->user();

        if (! $auth->isAdmin() && ! $auth->isDoctor()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if (! $user->isPatient()) {
            return response()->json(['message' => 'Cet utilisateur n’est pas un patient.'], 422);
        }

        $profile = PatientEmergencyProfile::where('user_id', $user->id)->first();

        return response()->json([
            'patient' => $user->only(['id', 'name', 'email', 'phone', 'birth_date', 'gender', 'address']),
            'emergency_profile' => $profile,
        ]);
    }
}
