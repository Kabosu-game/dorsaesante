<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teleconsultation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeleconsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Teleconsultation::with(['patient:id,name,avatar', 'doctor:id,name,avatar'])
            ->when($user->isPatient(), fn($q) => $q->where('patient_id', $user->id))
            ->when($user->isDoctor(), fn($q) => $q->where('doctor_id', $user->id))
            ->latest();

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id'      => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        $data['patient_id'] = $request->user()->id;
        $data['room_token'] = Str::uuid();

        $consult = Teleconsultation::create($data);

        return response()->json([
            'message'    => 'Téléconsultation créée.',
            'session'    => $consult->load('doctor:id,name,avatar'),
            'room_token' => $consult->room_token,
        ], 201);
    }

    public function show(Teleconsultation $teleconsultation)
    {
        $user = request()->user();
        if ($teleconsultation->patient_id !== $user->id && $teleconsultation->doctor_id !== $user->id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($teleconsultation->load(['patient:id,name,avatar', 'doctor:id,name,avatar', 'appointment']));
    }

    public function start(Request $request, Teleconsultation $teleconsultation)
    {
        if ($teleconsultation->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Seul le médecin peut démarrer la session.'], 403);
        }

        $teleconsultation->update(['status' => 'active', 'started_at' => now()]);

        return response()->json(['message' => 'Session démarrée.', 'session' => $teleconsultation]);
    }

    public function end(Request $request, Teleconsultation $teleconsultation)
    {
        if ($teleconsultation->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $data = $request->validate([
            'doctor_notes'       => 'nullable|string',
            'prescription_file'  => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('prescription_file')) {
            $data['prescription_file'] = $request->file('prescription_file')->store('prescriptions', 'public');
        }

        $data['status'] = 'completed';
        $data['ended_at'] = now();

        $teleconsultation->update($data);

        return response()->json(['message' => 'Consultation terminée.', 'session' => $teleconsultation]);
    }
}
