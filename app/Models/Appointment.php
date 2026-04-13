<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'health_structure_id', 'scheduled_at',
        'duration_minutes', 'type', 'status', 'reason', 'notes',
        'reminder_sent', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reminder_sent' => 'boolean',
        ];
    }

    public function patient() { return $this->belongsTo(User::class, 'patient_id'); }
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function healthStructure() { return $this->belongsTo(HealthStructure::class); }
    public function teleconsultation() { return $this->hasOne(Teleconsultation::class); }
    public function medicalRecord() { return $this->hasOne(MedicalRecord::class); }
}
