<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = MedicalRecord::with(['doctor:id,name,avatar', 'appointment'])
            ->when($user->isPatient(), fn($q) => $q->where('patient_id', $user->id))
            ->when($user->isDoctor(), fn($q) => $q->where('doctor_id', $user->id))
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->latest('record_date');

        return response()->json($query->paginate(20));
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $user = request()->user();
        if ($medicalRecord->patient_id !== $user->id && $medicalRecord->doctor_id !== $user->id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return response()->json($medicalRecord->load(['patient:id,name', 'doctor:id,name,avatar', 'appointment']));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isDoctor() && ! $user->isAdmin()) {
            return response()->json(['message' => 'Réservé aux médecins.'], 403);
        }

        $data = $request->validate([
            'patient_id'     => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'type'           => 'required|in:consultation,lab_result,prescription,imaging,vaccination',
            'title'          => 'required|string|max:255',
            'content'        => 'nullable|string',
            'record_date'    => 'required|date',
            'diagnosis'      => 'nullable|string|max:255',
            'medications'    => 'nullable|array',
            'follow_up_notes'=> 'nullable|string',
            'is_confidential'=> 'boolean',
        ]);

        $data['doctor_id'] = $user->id;

        // Gérer les pièces jointes
        if ($request->hasFile('attachments')) {
            $paths = [];
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('medical-records', 'public');
            }
            $data['attachments'] = $paths;
        }

        $record = MedicalRecord::create($data);

        return response()->json(['message' => 'Dossier médical créé.', 'record' => $record], 201);
    }

    public function patientRecords(Request $request, int $patientId)
    {
        $user = $request->user();

        if (! $user->isDoctor() && ! $user->isAdmin() && $user->id !== $patientId) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $records = MedicalRecord::where('patient_id', $patientId)
            ->with('doctor:id,name,avatar')
            ->latest('record_date')
            ->paginate(20);

        return response()->json($records);
    }
}
