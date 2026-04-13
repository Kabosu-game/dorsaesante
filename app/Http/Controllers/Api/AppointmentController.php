<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Appointment::with(['patient:id,name,phone,avatar', 'doctor:id,name,avatar', 'healthStructure:id,name,address'])
            ->when($user->isPatient(), fn($q) => $q->where('patient_id', $user->id))
            ->when($user->isDoctor(), fn($q) => $q->where('doctor_id', $user->id))
            ->latest('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id'           => 'required|exists:users,id',
            'health_structure_id' => 'nullable|exists:health_structures,id',
            'scheduled_at'        => 'required|date|after:now',
            'duration_minutes'    => 'integer|min:15|max:120',
            'type'                => 'in:in_person,teleconsultation',
            'reason'              => 'nullable|string|max:500',
        ]);

        $data['patient_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $appointment = Appointment::create($data);

        return response()->json([
            'message'     => 'Rendez-vous créé avec succès.',
            'appointment' => $appointment->load('doctor:id,name,avatar', 'healthStructure:id,name,address'),
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorizeAppointment($appointment);

        return response()->json($appointment->load(['patient:id,name,phone,avatar', 'doctor:id,name,avatar', 'healthStructure', 'teleconsultation']));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        $request->validate([
            'status'              => 'required|in:confirmed,cancelled,completed,no_show',
            'cancellation_reason' => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        // Seul le médecin peut confirmer/compléter, le patient peut annuler
        if (in_array($request->status, ['confirmed', 'completed', 'no_show']) && ! $user->isDoctor() && ! $user->isAdmin()) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $appointment->update($request->only('status', 'cancellation_reason', 'notes'));

        return response()->json(['message' => 'Statut mis à jour.', 'appointment' => $appointment]);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if ($appointment->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($appointment->status === 'completed') {
            return response()->json(['message' => 'Impossible d\'annuler un rendez-vous terminé.'], 422);
        }

        $appointment->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
        ]);

        return response()->json(['message' => 'Rendez-vous annulé.']);
    }

    private function authorizeAppointment(Appointment $appointment): void
    {
        $user = request()->user();
        if ($user->isAdmin()) return;
        if ($appointment->patient_id !== $user->id && $appointment->doctor_id !== $user->id) {
            abort(403, 'Non autorisé.');
        }
    }
}
